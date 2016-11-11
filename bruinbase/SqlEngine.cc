/**
 * Copyright (C) 2008 by The Regents of the University of California
 * Redistribution of this file is permitted under the terms of the GNU
 * Public License (GPL).
 *
 * @author Junghoo "John" Cho <cho AT cs.ucla.edu>
 * @date 3/24/2008
 */

#include <cstdio>
#include <iostream>
#include <fstream>
#include "Bruinbase.h"
#include "SqlEngine.h"
#include "BTreeIndex.h"

using namespace std;

// external functions and variables for load file and sql command parsing 
extern FILE* sqlin;
int sqlparse(void);


RC SqlEngine::run(FILE* commandline)
{
  fprintf(stdout, "Bruinbase> ");

  // set the command line input and start parsing user input
  sqlin = commandline;
  sqlparse();  // sqlparse() is defined in SqlParser.tab.c generated from
               // SqlParser.y by bison (bison is GNU equivalent of yacc)

  return 0;
}

RC SqlEngine::select(int attr, const string& table, const vector<SelCond>& cond)
{
  RecordFile rf;   // RecordFile containing the table
  RecordId   rid;  // record cursor for table scanning

  RC     rc;
  int    key;     
  string value;
  int    count;
  int    diff;

  BTreeIndex selectIndex;
  IndexCursor curCursor;
  bool chooseInit = false;

  int initialCheck;
  RC checkOpen = selectIndex.open(table+".idx", 'r');

  if(!checkOpen)
  {
	  for(unsigned int i = 0; i < cond.size(); i++)
	  {
		  if(cond[i].attr == 2)
			  continue;

		  if(cond[i].comp == SelCond::EQ)
		  {
			  initialCheck = i;
			  chooseInit = true;
			  break;
		  }

		  if((cond[i].comp == SelCond::GT || cond[i].comp == SelCond::GE) && (!chooseInit ||atoi(cond[initialCheck].value) < atoi(cond[i].value)))
		  {
			initialCheck = i;
			chooseInit = true;
//			continue;
		  }

		  /*if(cond[i].comp == SelCond::LT || cond[i].comp == SelCond::LE)
		  {
			  if(!chooseInit)
			  {
				  initialCheck = i;
				  continue;
			  }
		  }*/
	  }

	  if(chooseInit)
	  {
		  selectIndex.locate(atoi(cond[initialCheck].value), curCursor);
	  }
	  else
		  selectIndex.locate(0, curCursor);
	  count = 0;

	  //RC checkRead = selectIndex.readForward(curCursor, key, rid);

	  if(cond.size() == 0 && attr == 4)
	  {
		  while(!selectIndex.readForward(curCursor, key, rid))
			  count++;
		  goto end;
	  }

	  // open the table file
	  if ((rc = rf.open(table + ".tbl", 'r')) < 0) {
		fprintf(stderr, "Error: table %s does not exist\n", table.c_str());
	    return rc;
	  }

	  while(!selectIndex.readForward(curCursor, key, rid))
	  {
		if ((rc = rf.read(rid, key, value)) < 0) {
		  fprintf(stderr, "Error: while reading a tuple from table %s\n", table.c_str());
		  goto exit_select;
		} 

		  // scan the table file from the beginning
		// check the conditions on the tuple
		for (unsigned i = 0; i < cond.size(); i++) {
			// compute the difference between the tuple value and the condition value
			switch (cond[i].attr) {
			case 1:
				diff = key - atoi(cond[i].value);
				break;
			case 2:
				diff = strcmp(value.c_str(), cond[i].value);
				break;
			}

			// skip the tuple if any condition is not met
			switch (cond[i].comp) {
			case SelCond::EQ: //Stop when past equal
				if (diff != 0)
				{
					if(cond[i].attr == 1) 
						goto end;
					else
						goto next_rid;
				}
				break;
			case SelCond::NE: //Go to end
				if (diff == 0) goto next_rid;
				break;
			case SelCond::GT: //Go to end
				if (diff <= 0) goto next_rid;
				break;
			case SelCond::LT: //Stop at equal
				if (diff >= 0) 
					if(cond[i].attr == 1) 
						goto end;
					else
						goto next_rid;
				break;
			case SelCond::GE: //Go to end
				if (diff < 0) goto next_rid;
				break;
			case SelCond::LE: //Stop at greater
				if (diff > 0)
					if(cond[i].attr == 1) 
						goto end;
					else
						goto next_rid;
				break;
			}
		}
		// the condition is met for the tuple. 
		// increase matching tuple counter
		count++;

		// print the tuple a
		switch (attr) {
		case 1:  // SELECT key
			fprintf(stdout, "%d\n", key);
			break;
		case 2:  // SELECT value
			fprintf(stdout, "%s\n", value.c_str());
			break;
		case 3:  // SELECT *
			fprintf(stdout, "%d '%s'\n", key, value.c_str());
			break;
		}

		// move to the next tuple
		next_rid:
			count = count;
			//checkRead = selectIndex.readForward(curCursor, key, rid);
			
	  }
  }
  else{
	  // open the table file
	  if ((rc = rf.open(table + ".tbl", 'r')) < 0) {
		fprintf(stderr, "Error: table %s does not exist\n", table.c_str());
		return rc;
	  }
	  // scan the table file from the beginning
	  rid.pid = rid.sid = 0;
	  count = 0;
	  while (rid < rf.endRid()) {
		// read the tuple
		if ((rc = rf.read(rid, key, value)) < 0) {
		  fprintf(stderr, "Error: while reading a tuple from table %s\n", table.c_str());
		  goto exit_select;
		} 
		// check the conditions on the tuple
		for (unsigned i = 0; i < cond.size(); i++) {
		  // compute the difference between the tuple value and the condition value
		switch (cond[i].attr) {
		case 1:
		  diff = key - atoi(cond[i].value);
		  break;
		case 2:
		  diff = strcmp(value.c_str(), cond[i].value);
		  break;
		}

		  // skip the tuple if any condition is not met
		switch (cond[i].comp) {
		case SelCond::EQ:
		  if (diff != 0) goto next_tuple;
		  break;
		case SelCond::NE:
		  if (diff == 0) goto next_tuple;
		  break;
		case SelCond::GT:
		  if (diff <= 0) goto next_tuple;
		  break;
		case SelCond::LT:
		  if (diff >= 0) goto next_tuple;
		  break;
		case SelCond::GE:
		  if (diff < 0) goto next_tuple;
		  break;
		case SelCond::LE:
		  if (diff > 0) goto next_tuple;
		  break;
		  }
		}

		// the condition is met for the tuple. 
		// increase matching tuple counter
		count++;

		// print the tuple 
		switch (attr) {
		case 1:  // SELECT key
		  fprintf(stdout, "%d\n", key);
		  break;
		case 2:  // SELECT value
		  fprintf(stdout, "%s\n", value.c_str());
		  break;
		case 3:  // SELECT *
		  fprintf(stdout, "%d '%s'\n", key, value.c_str());
		  break;
		}

		// move to the next tuple
		next_tuple:
		++rid;
	  }
  }
	  // print matching tuple count if "select count(*)"
  end:
  if (attr == 4) {
	fprintf(stdout, "%d\n", count);
  }
  rc = 0;

  // close the table file and return
exit_select:
  selectIndex.close();
  rf.close();
  return rc;
}

RC SqlEngine::load(const string& table, const string& loadfile, bool index)
{
  BTreeIndex BTree;

  if(index)
  {
	if(BTree.open(table+".idx", 'w'))
	{
		return RC_FILE_OPEN_FAILED;
	}
  }

  RecordFile new_record;

  if(new_record.open(table+".tbl", 'w'))
  {
	  return RC_FILE_OPEN_FAILED;
  }

  RecordId ret_id;
  string cur_line;
  int cur_key;
  string cur_value;

  ifstream read_file;
  read_file.open(loadfile.c_str());

  if(read_file.is_open())
  {
	  while(getline(read_file, cur_line))
	  {
		  parseLoadLine(cur_line, cur_key, cur_value);
		  new_record.append(cur_key, cur_value, ret_id);
		  if(index)
		  {
			  RC insertCheck = BTree.insert(cur_key, ret_id);
			  if(insertCheck)
				  return insertCheck;
		  }
	  }
	  read_file.close();
  }
  else
  {
	  return RC_FILE_READ_FAILED;
  }

  new_record.close();
  BTree.close();

  return 0;
}

RC SqlEngine::parseLoadLine(const string& line, int& key, string& value)
{
    const char *s;
    char        c;
    string::size_type loc;
    
    // ignore beginning white spaces
    c = *(s = line.c_str());
    while (c == ' ' || c == '\t') { c = *++s; }

    // get the integer key value
    key = atoi(s);

    // look for comma
    s = strchr(s, ',');
    if (s == NULL) { return RC_INVALID_FILE_FORMAT; }

    // ignore white spaces
    do { c = *++s; } while (c == ' ' || c == '\t');
    
    // if there is nothing left, set the value to empty string
    if (c == 0) { 
        value.erase();
        return 0;
    }

    // is the value field delimited by ' or "?
    if (c == '\'' || c == '"') {
        s++;
    } else {
        c = '\n';
    }

    // get the value string
    value.assign(s);
    loc = value.find(c, 0);
    if (loc != string::npos) { value.erase(loc); }

    return 0;
}
