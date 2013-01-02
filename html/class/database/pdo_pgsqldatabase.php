<?php
// $Id: pgsqldatabase.php,v 1.2 2008/09/20 16:04:40 mumincacao Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
/**
 * @package     kernel
 * @subpackage  database
 * 
 * @author	    aotake <aotake@bmath.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

/**
 * base class
 */
include_once XOOPS_ROOT_PATH.'/class/database/pdodatabase.php';

if (!defined('PGSQL_CLIENT_FOUND_ROWS')) {
	define('PGSQL_CLIENT_FOUND_ROWS', 2);
}

/**
 * connection to a pgsql database
 * 
 * @abstract
 * 
 * @author      Kazumi Ono  <onokazu@xoops.org>
 * @copyright   copyright (c) 2000-2003 XOOPS.org
 * 
 * @package     kernel
 * @subpackage  database
 */
class XoopsPdoPgsqlDatabase extends XoopsPdoDatabase
{
	/**
	 * connect to the database
	 * 
     * @param bool $selectdb select the database now?
     * @return bool successful?
	 */
	function connect($selectdb = true)
	{
        $port = defined("XOOPS_DB_PORT") ? XOOPS_DB_PORT : 5432;

        $dsn = "pgsql:host=" . XOOPS_DB_HOST.";";
        $dsn.= "port=". $port . ";";
        $dsn.= "dbname=" . XOOPS_DB_NAME . ";";
        //$dsn.= "charset=" . XOOPS_DB_CHARSET . ";";
        $dsn.= "user=" . XOOPS_DB_USER . ";";
        $dsn.= "password=" . XOOPS_DB_PASS;

        try {
            $this->conn = new PDO($dsn);
            return true;
        }
        catch(PDOException $e){
            $this->result = $e;
			$this->logger->addQuery('', $this->error(), $this->errno());
            //$logger->crit($e->getMessage());
            return false;
        }
	}

	/**
	 * generate an ID for a new row
     * 
     * This is for compatibility only. Will always return 0, because PgSQL supports
     * autoincrement for primary keys.
     * 
     * @param string $sequence name of the sequence from which to get the next ID
     * @return int always 0, because pgsql has support for autoincrement
	 */
	function genId($sequence)
	{
        $seqName = $this->prefix($sequence);
        $sth = $this->conn->prepare("select nextval('$seqName')");;
        $sth->execute();

        $res = $sth->fetch(PDO::FETCH_NUM);
        return (int)$res[0];
	}

	/**
	 * Get the ID generated from the previous INSERT operation
	 * 
     * @return int
	 */
	function getInsertId()
	{
        try {
    	    $sth = $this->conn->prepare("SELECT LASTVAL()");
            $sth->execute();
            $row = $sth->fetch( PDO::FETCH_ASSOC );
            return (int)$row["lastval"];
        } catch(PDOException $e) {
            // nextval() を使わない INSERT 時は lastval() できず
            //      ERROR: lastval is not yet defined in this session
            // というエラーを吐く。
            return 0;
        }
	}

}

/**
 * Safe Connection to a PgSQL database.
 * 
 * 
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000-2003 XOOPS.org
 * 
 * @package kernel
 * @subpackage database
 */
class XoopsPdoPgsqlDatabaseSafe extends XoopsPdoPgsqlDatabase
{

    /**
     * perform a query on the database
     * 
     * @param string $sql a valid PgSQL query
     * @param int $limit number of records to return
     * @param int $start offset of first record to return
     * @return resource query result or FALSE if successful
     * or TRUE if successful and no result
     */
	function &query($sql, $limit=0, $start=0)
	{
		$result =& $this->queryF($sql, $limit, $start);
		return $result;
	}
}

/**
 * Read-Only connection to a PgSQL database.
 * 
 * This class allows only SELECT queries to be performed through its 
 * {@link query()} method for security reasons.
 * 
 * 
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000-2003 XOOPS.org
 * 
 * @package kernel
 * @subpackage database
 */
class XoopsPdoPgsqlDatabaseProxy extends XoopsPdoPgsqlDatabase
{

    /**
     * perform a query on the database
     * 
     * this method allows only SELECT queries for safety.
     * 
     * @param string $sql a valid PgSQL query
     * @param int $limit number of records to return
     * @param int $start offset of first record to return
     * @return resource query result or FALSE if unsuccessful
     */
	function &query($sql, $limit=0, $start=0)
	{
	    $sql = ltrim($sql);
		if (preg_match('/^SELECT/i', $sql)) {
			$ret = $this->queryF($sql, $limit, $start);
			return $ret;
		}
		$this->logger->addQuery($sql, 'Database update not allowed during processing of a GET request', 0);
		
		$ret = false;
		return $ret;
	}
}
?>
