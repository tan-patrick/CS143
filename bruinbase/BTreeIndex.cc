/*
 * Copyright (C) 2008 by The Regents of the University of California
 * Redistribution of this file is permitted under the terms of the GNU
 * Public License (GPL).
 *
 * @author Junghoo "John" Cho <cho AT cs.ucla.edu>
 * @date 3/24/2008
 */

#include "BTreeIndex.h"
#include "BTreeNode.h"

using namespace std;

/*
 * BTreeIndex constructor
 */
BTreeIndex::BTreeIndex()
{
    rootPid = -1;
	treeHeight = 0;
}

/*
 * Open the index file in read or write mode.
 * Under 'w' mode, the index file should be created if it does not exist.
 * @param indexname[IN] the name of the index file
 * @param mode[IN] 'r' for read, 'w' for write
 * @return error code. 0 if no error
 */
RC BTreeIndex::open(const string& indexname, char mode)
{
    RC checkOpen = pf.open(indexname, mode);
	if(checkOpen)
		return checkOpen;

	if(pf.endPid() == 0)
	{
		rootPid = -1;
		treeHeight = 0;

		char inputData[PageFile::PAGE_SIZE];
		PageId* pageData = (PageId *) inputData;
		*pageData = rootPid;
		int* heightData = (int *) (inputData + sizeof(PageId));
		*heightData = treeHeight;

		RC checkWrite = pf.write(0, inputData);
		if(checkWrite)
			return checkWrite;
	}
	else 
	{
		char readData[PageFile::PAGE_SIZE];
		RC checkRead = pf.read(0, readData);
		if(checkRead)
			return checkRead;

		PageId* pageData = (PageId *) readData;
		rootPid = *pageData;
		int* heightData = (int *) (readData + sizeof(PageId));
		treeHeight = *heightData;
	}
	return 0;
}

/*
 * Close the index file.
 * @return error code. 0 if no error
 */
RC BTreeIndex::close()
{
    char inputData[PageFile::PAGE_SIZE];
	PageId* pageData = (PageId *) inputData;
	*pageData = rootPid;
	int* heightData = (int *) (inputData + sizeof(PageId));
	*heightData = treeHeight;

	RC checkWrite = pf.write(0, inputData);
	if(checkWrite)
		return checkWrite;
}

/*
 * Insert (key, RecordId) pair to the index.
 * @param key[IN] the key for the value inserted into the index
 * @param rid[IN] the RecordId for the record being inserted into the index
 * @return error code. 0 if no error
 */
RC BTreeIndex::insert(int key, const RecordId& rid)
{
	if(key < 0)
		return RC_INVALID_ATTRIBUTE;

	if(rid.pid < 0 || rid.sid < 0)
		return RC_INVALID_PID;

	if(treeHeight > 0)
	{
		int retKey;
		int retPid;
		bool nodeFull = false;
		RC checkInsert = insertRecursion(key, rid, rootPid, 1, nodeFull, retKey, retPid);
		if(checkInsert)
			return checkInsert;
		if(nodeFull)
		{
			BTNonLeafNode rootNode;
			rootNode.initializeRoot(rootPid, retKey, retPid);
			rootPid = pf.endPid();
			treeHeight++;
			RC checkWrite = rootNode.write(rootPid,pf);
			if(checkWrite)
				return checkWrite;
		}
	}
	else
	{
		BTLeafNode rootNode;
		rootPid = pf.endPid();
		if(rootPid <= 0)
			rootPid = 1;

		RC checkInsert = rootNode.insert(key, rid);
		if(checkInsert)
			return checkInsert;
		rootNode.setNextNodePtr(-1);
		RC checkWrite = rootNode.write(rootPid, pf);
		if(checkWrite)
			return checkWrite;
		treeHeight = 1;
	}

    return 0;
}

RC BTreeIndex::insertRecursion(int key, const RecordId& rid, PageId pid, int height, bool& nodeFull, int& retKey, int& retPid)
{
	if(height < treeHeight)
	{
		BTNonLeafNode curNode;
		RC checkRead = curNode.read(pid, pf);
		if(checkRead)
			return checkRead;

		RC checkLocate = curNode.locateChildPtr(key, pid);
		if(checkLocate)
			return checkLocate;

		insertRecursion(key, rid, pid, height + 1, nodeFull, retKey, retPid);

		if(nodeFull)
		{
			RC checkInsert = curNode.insert(retKey, retPid);
			if(checkInsert)
			{
				BTNonLeafNode siblingNode;
				RC checkSplit = curNode.insertAndSplit(retKey, retPid, siblingNode, retKey);
				if(checkSplit)
					return checkSplit;
				retPid = pf.endPid();
				RC checkSiblingWrite = siblingNode.write(retPid, pf);
				if(checkSiblingWrite)
					return checkSiblingWrite;
			}
			else
				nodeFull = false;
		}
	}
	else
	{
		BTLeafNode leafNode;
		RC checkRead = leafNode.read(pid, pf);
		if(checkRead)
			return checkRead;

		RC checkInsert = leafNode.insert(key, rid);
		if(checkInsert)
		{
			BTLeafNode siblingNode;
			nodeFull = true;
			retPid = pf.endPid();
			RC checkSplit = leafNode.insertAndSplit(key, rid, siblingNode, retKey);
			if(checkSplit)
				return checkSplit;

			siblingNode.setNextNodePtr(leafNode.getNextNodePtr());
			leafNode.setNextNodePtr(retPid);

			RC checkLeafWrite = leafNode.write(pid, pf);
			if(checkLeafWrite)
				return checkLeafWrite;

			RC checkSiblingWrite = siblingNode.write(retPid, pf);
			if(checkSiblingWrite)
				return checkSiblingWrite;
		}

		RC checkWrite = leafNode.write(pid, pf);
		if(checkWrite)
			return checkWrite;
	}
}

/*
 * Find the leaf-node index entry whose key value is larger than or 
 * equal to searchKey, and output the location of the entry in IndexCursor.
 * IndexCursor is a "pointer" to a B+tree leaf-node entry consisting of
 * the PageId of the node and the SlotID of the index entry.
 * Note that, for range queries, we need to scan the B+tree leaf nodes.
 * For example, if the query is "key > 1000", we should scan the leaf
 * nodes starting with the key value 1000. For this reason,
 * it is better to return the location of the leaf node entry 
 * for a given searchKey, instead of returning the RecordId
 * associated with the searchKey directly.
 * Once the location of the index entry is identified and returned 
 * from this function, you should call readForward() to retrieve the
 * actual (key, rid) pair from the index.
 * @param key[IN] the key to find.
 * @param cursor[OUT] the cursor pointing to the first index entry
 *                    with the key value.
 * @return error code. 0 if no error.
 */
RC BTreeIndex::locate(int searchKey, IndexCursor& cursor)
{
	if(searchKey < 0)
		return RC_INVALID_ATTRIBUTE;

	int curHeight = 1;
	PageId curPageId = rootPid;
	int keyEid;
	BTNonLeafNode curNode;
	BTLeafNode finalNode;
	while(curHeight < treeHeight)
	{
		RC checkRead = curNode.read(curPageId, pf);
		if(checkRead)
			return checkRead;

		RC checkLocate = curNode.locateChildPtr(searchKey, curPageId);
		if(checkLocate)
			return checkLocate;
		curHeight++;
	}

	RC checkLeafRead = finalNode.read(curPageId, pf);
	if(checkLeafRead)
	{
		return checkLeafRead;
	}
	RC checkLeafLocate = finalNode.locate(searchKey, keyEid);
	if(checkLeafLocate)
		return checkLeafLocate;
	cursor.pid = curPageId;
	cursor.eid = keyEid;
    return 0;
}

/*
 * Read the (key, rid) pair at the location specified by the index cursor,
 * and move foward the cursor to the next entry.
 * @param cursor[IN/OUT] the cursor pointing to an leaf-node index entry in the b+tree
 * @param key[OUT] the key stored at the index cursor location.
 * @param rid[OUT] the RecordId stored at the index cursor location.
 * @return error code. 0 if no error
 */
RC BTreeIndex::readForward(IndexCursor& cursor, int& key, RecordId& rid)
{
	if(cursor.eid < 0 || cursor.pid < 0 || cursor.pid >= pf.endPid() || cursor.pid == -1)
		return RC_INVALID_CURSOR;

	BTLeafNode readNode;

	RC checkRead = readNode.read(cursor.pid, pf);
	if(checkRead)
		return checkRead;

	RC checkEntry = readNode.readEntry(cursor.eid, key, rid);
	if(checkEntry)
		return checkEntry;

	cursor.eid = cursor.eid + 1;
	if(cursor.eid >= readNode.getKeyCount() || key == 0)
	{
		cursor.eid = 0;
		cursor.pid = readNode.getNextNodePtr();
	}
	return 0;
}
