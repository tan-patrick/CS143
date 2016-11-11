#include "BTreeNode.h"

struct BTLeafNode::Pair
{
	RecordId rid;
	int key;
};

BTLeafNode::BTLeafNode()
{
	maxNumKeys = (PageFile::PAGE_SIZE-sizeof(PageId))/(sizeof(Pair));
	memset(buffer, 0, sizeof(buffer));
}

/*
 * Read the content of the node from the page pid in the PageFile pf.
 * @param pid[IN] the PageId to read
 * @param pf[IN] PageFile to read from
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::read(PageId pid, const PageFile& pf)
{ return pf.read(pid, buffer); }
    
/*
 * Write the content of the node to the page pid in the PageFile pf.
 * @param pid[IN] the PageId to write to
 * @param pf[IN] PageFile to write to
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::write(PageId pid, PageFile& pf)
{ return pf.write(pid, buffer); }

/*
 * Return the number of keys stored in the node.
 * @return the number of keys in the node
 */
int BTLeafNode::getKeyCount()
{ 
	Pair* curPair = (Pair *) buffer;
	int count = 0;
	while(curPair->key > 0 && count < maxNumKeys) //Key must be a positive number, according to instructions
	{
		count++;
		curPair++;
	}
	return count;
}

/*
 * Insert a (key, rid) pair to the node.
 * @param key[IN] the key to insert
 * @param rid[IN] the RecordId to insert
 * @return 0 if successful. Return an error code if the node is full.
 */
RC BTLeafNode::insert(int key, const RecordId& rid)
{
	if(key < 0)
		return RC_INVALID_ATTRIBUTE;
	if(getKeyCount() >= maxNumKeys)
	{	
		return RC_INVALID_ATTRIBUTE;
	}

	int insertNum = 0;
	if(locate(key, insertNum) == RC_NO_SUCH_RECORD)
		insertNum = getKeyCount();

	Pair* finalPair = (Pair *) buffer + getKeyCount();
	Pair* curPair = (Pair *) buffer + insertNum;
	Pair* movePair = curPair;
	Pair* storePair = curPair;
	Pair placePair;

	while(curPair != finalPair)
	{
		movePair = curPair + 1;
		placePair = *storePair;
		*storePair = *movePair;
		*movePair = placePair;
		curPair = curPair + 1;
	}

	Pair* insertPair = (Pair *) buffer + insertNum;
	insertPair->key = key;
	insertPair->rid = rid;
	return 0;
}

/*
 * Insert the (key, rid) pair to the node
 * and split the node half and half with sibling.
 * The first key of the sibling node is returned in siblingKey.
 * @param key[IN] the key to insert.
 * @param rid[IN] the RecordId to insert.
 * @param sibling[IN] the sibling node to split with. This node MUST be EMPTY when this function is called.
 * @param siblingKey[OUT] the first key in the sibling node after split.
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::insertAndSplit(int key, const RecordId& rid, 
                              BTLeafNode& sibling, int& siblingKey)
{
	if(key < 0)
		return RC_INVALID_ATTRIBUTE;
	if(sibling.getKeyCount() != 0)
		return RC_INVALID_ATTRIBUTE;

	int insertNum;
	int siblingEid = (maxNumKeys + 1)/ 2; //Leave extra element on left node
	int insertEid;
	locate(key, insertEid);

	if(insertEid <= siblingEid)
		siblingKey = ((Pair *)(buffer) + siblingEid - 1)->key;
	else
		siblingKey = ((Pair *)(buffer) + siblingEid)->key;

	Pair holdSibling;
	holdSibling.key = key;
	holdSibling.rid = rid;

	Pair* curPair = (Pair *)buffer + insertEid;
	Pair movePair;

	while(insertEid < siblingEid)
	{
		movePair = *curPair;
		*curPair = holdSibling;
		holdSibling = movePair;
		insertEid++;
		curPair++;
	}
	
	curPair = (Pair *)buffer + siblingEid;
	int reset = 0;
	RecordId resetRid;
	resetRid.pid = 0;
	resetRid.sid = 0;

	for(int i = siblingEid; i < maxNumKeys; i++)
	{
		sibling.insert(curPair->key, curPair->rid);
		curPair->key = reset;
		curPair->rid = resetRid;
		curPair++;
	}
	
	sibling.insert(holdSibling.key, holdSibling.rid);

	return 0;
}
#include <cstdio>
#include <iostream>
#include <fstream>
#include "Bruinbase.h"
#include "SqlEngine.h"
#include "BTreeIndex.h"
using namespace std;
/*
 * Find the entry whose key value is larger than or equal to searchKey
 * and output the eid (entry number) whose key value >= searchKey.
 * Remeber that all keys inside a B+tree node should be kept sorted.
 * @param searchKey[IN] the key to search for
 * @param eid[OUT] the entry number that contains a key larger than or equalty to searchKey
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::locate(int searchKey, int& eid)
{
	if(searchKey < 0)
		return RC_INVALID_ATTRIBUTE;
	eid = 0;
	Pair* curPair = (Pair *) buffer;
	while(curPair->key < searchKey && eid < getKeyCount())
	{
		if(curPair->key >= searchKey)
		{
			break;
		}
		eid++;
		curPair++;
	}
	if(eid == getKeyCount())
	{
		return RC_NO_SUCH_RECORD;
	}

	return 0;
}

/*
 * Read the (key, rid) pair from the eid entry.
 * @param eid[IN] the entry number to read the (key, rid) pair from
 * @param key[OUT] the key from the entry
 * @param rid[OUT] the RecordId from the entry
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::readEntry(int eid, int& key, RecordId& rid)
{
	if(eid >= getKeyCount() || eid < 0)
		return RC_INVALID_ATTRIBUTE;

	Pair* readPair = (Pair *) buffer + eid;

	key = readPair->key;
	rid = readPair->rid;

	if(key == 0)
		return RC_INVALID_ATTRIBUTE;

	return 0;
}

/*
 * Return the pid of the next slibling node.
 * @return the PageId of the next sibling node 
 */
PageId BTLeafNode::getNextNodePtr()
{ 
	PageId* pid = (PageId *)(buffer + PageFile::PAGE_SIZE);
	pid = pid - 1;
	return *pid;
}

/*
 * Set the pid of the next slibling node.
 * @param pid[IN] the PageId of the next sibling node 
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::setNextNodePtr(PageId pid)
{
	PageId* setpid = (PageId *)(buffer + PageFile::PAGE_SIZE);
	setpid = setpid - 1;
	*setpid = pid;
	return 0;
}

struct BTNonLeafNode::Pair
{
	PageId pid;
	int key;
};

BTNonLeafNode::BTNonLeafNode()
{
	maxNumKeys = (PageFile::PAGE_SIZE-sizeof(PageId))/(sizeof(Pair));
	memset(buffer, 0, sizeof(buffer));
}

/*
 * Read the content of the node from the page pid in the PageFile pf.
 * @param pid[IN] the PageId to read
 * @param pf[IN] PageFile to read from
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::read(PageId pid, const PageFile& pf)
{ return pf.read(pid, buffer); }
    
/*
 * Write the content of the node to the page pid in the PageFile pf.
 * @param pid[IN] the PageId to write to
 * @param pf[IN] PageFile to write to
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::write(PageId pid, PageFile& pf)
{ return pf.write(pid, buffer); }

/*
 * Return the number of keys stored in the node.
 * @return the number of keys in the node
 */
int BTNonLeafNode::getKeyCount()
{ 
	Pair* curPair = (Pair *) buffer;
	int count = 0;
	while(curPair->key > 0 && count < maxNumKeys) //Key must be a positive number, according to instructions
	{
		count++;
		curPair++;
	}
	return count;
}


/*
 * Insert a (key, pid) pair to the node.
 * @param key[IN] the key to insert
 * @param pid[IN] the PageId to insert
 * @return 0 if successful. Return an error code if the node is full.
 */
RC BTNonLeafNode::insert(int key, PageId pid)
{
	if(key < 0 || pid < 0)
		return RC_INVALID_ATTRIBUTE;
	if(getKeyCount() >= maxNumKeys)
	{
		return RC_INVALID_ATTRIBUTE;
	}

	int insertNum = 0;
	Pair* findInsert = (Pair *) buffer;

	while(findInsert->key < key && insertNum < getKeyCount())
	{
		if(findInsert->key >= key)
			break;
		findInsert++;
		insertNum++;
	}

	Pair* finalPair = (Pair *) buffer + getKeyCount(); //note: one more pid after final pair
	Pair* curPair = (Pair *) buffer + insertNum;
	Pair* movePair = curPair;
	int storeKey = curPair->key;
	PageId storePid = (curPair+1)->pid;
	int insertKey;
	int insertPid;
	curPair->key = key;
	(curPair+1)->pid = pid;

	while(curPair != finalPair)
	{
		movePair = curPair + 1;
		insertKey = storeKey;
		insertPid = storePid;
		storeKey = movePair->key;
		storePid = (movePair+1)->pid;
		movePair->key = insertKey;
		(movePair+1)->pid = insertPid;
		curPair = curPair + 1;
	}

	return 0;
}

/*
 * Insert the (key, pid) pair to the node
 * and split the node half and half with sibling.
 * The middle key after the split is returned in midKey.
 * @param key[IN] the key to insert
 * @param pid[IN] the PageId to insert
 * @param sibling[IN] the sibling node to split with. This node MUST be empty when this function is called.
 * @param midKey[OUT] the key in the middle after the split. This key should be inserted to the parent node.
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::insertAndSplit(int key, PageId pid, BTNonLeafNode& sibling, int& midKey)
{
	if(key < 0 || pid < 0)
		return RC_INVALID_PID;
	if(sibling.getKeyCount() != 0)
		return RC_INVALID_ATTRIBUTE;

	int midId = (maxNumKeys + 1)/ 2; //Leave extra element on left node

	int insertNum = 0;
	Pair* findInsert = (Pair *) buffer;

	while(findInsert->key < key && insertNum < getKeyCount())
	{
		if(findInsert->key >= key)
			break;
		findInsert++;
		insertNum++;
	}

	if(insertNum <= midId)
		midKey = ((Pair *)(buffer) + midId - 1)->key;
	else
		midKey = ((Pair *)(buffer) + midId)->key;

	PageId firstRoot;
	if(insertNum <= midId)
	{
		Pair* midPair = (Pair *) buffer + midId;
		Pair* curPair = (Pair *) buffer + insertNum;
		Pair* movePair = curPair;
		int storeKey = curPair->key;
		PageId storePid = (curPair+1)->pid;
		int insertKey;
		int insertPid;
		curPair->key = key;
		(curPair+1)->pid = pid;

		while(curPair < midPair - 1)
		{
			movePair = curPair + 1;
			insertKey = storeKey;
			insertPid = storePid;
			storeKey = movePair->key;
			storePid = (movePair+1)->pid;
			movePair->key = insertKey;
			(movePair+1)->pid = insertPid;
			curPair = curPair + 1;
		}
		firstRoot = storePid;
	}
	else
	{
		Pair* rootPair = (Pair *)buffer + midId;
		firstRoot = rootPair->pid;
	}

	Pair* curPair = (Pair *)buffer + midId;
	int reset = 0;
	PageId resetPid = 0;

	if(insertNum <= midId)
	{
		PageId secondRoot = (curPair + 1)->pid;
		int rootKey = curPair->key;
		sibling.initializeRoot(firstRoot, rootKey, secondRoot);
		curPair->key = reset;
		(curPair + 1)->pid = resetPid;

		curPair++;

		for(int i = midId + 1; i < maxNumKeys; i++)
		{
			sibling.insert(curPair->key, (curPair + 1)->pid);
			curPair->key = reset;
			(curPair + 1)->pid = resetPid;
			curPair++;
		}
	}
	else
	{

		firstRoot = (curPair + 1)->pid;
		PageId secondRoot = (curPair + 2)->pid;
		int rootKey = (curPair + 1)->key;
		sibling.initializeRoot(firstRoot, rootKey, secondRoot);
		curPair->key = reset;
		(curPair + 1)->key = reset;
		(curPair + 1)->pid = resetPid;
		(curPair + 2)->pid = resetPid;
		sibling.insert(key, pid);

		curPair += 2;

		for(int i = midId + 2; i < maxNumKeys; i++)
		{
			sibling.insert(curPair->key, (curPair + 1)->pid);
			curPair->key = reset;
			(curPair + 1)->pid = resetPid;
			curPair++;
		}
	}
	
	return 0;
}

/*
 * Given the searchKey, find the child-node pointer to follow and
 * output it in pid.
 * @param searchKey[IN] the searchKey that is being looked up.
 * @param pid[OUT] the pointer to the child node to follow.
 * @return 0 if successful. Return an error code if there is an error.
 */

RC BTNonLeafNode::locateChildPtr(int searchKey, PageId& pid)
{
	if(searchKey < 0)
		return RC_INVALID_ATTRIBUTE;
	int eid = 0;
	Pair* curPair = (Pair *) buffer;
	while(searchKey > curPair->key && eid < getKeyCount())
	{
		if(curPair->key >= searchKey)
		{
			break;
		}
		curPair++;
		eid++;
	}
	if(eid == getKeyCount())
	{
		pid = curPair->pid;
	}

	if(curPair->key == searchKey)
	{
		curPair++;
		pid = curPair->pid;
	}
	else if(curPair->key > searchKey)
		pid = curPair->pid;

	return 0;
}

/*
 * Initialize the root node with (pid1, key, pid2).
 * @param pid1[IN] the first PageId to insert
 * @param key[IN] the key that should be inserted between the two PageIds
 * @param pid2[IN] the PageId to insert behind the key
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::initializeRoot(PageId pid1, int key, PageId pid2)
{
	if(key < 0 || pid1 < 0 || pid2 < 0)
		return RC_INVALID_PID;
	Pair* insertRoot = (Pair *) buffer;
	insertRoot->pid = pid1;
	insertRoot->key = key;
	insertRoot++;
	insertRoot->pid = pid2;
	return 0;
}
