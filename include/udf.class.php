<?php
/* Copyright (C) 2009 Technikum-Wien
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, USA.
 *
 * Authors: Bison Paolo <bison@technikum-wien.at>
 */

require_once(dirname(__FILE__).'/basis_db.class.php');
require_once(dirname(__FILE__).'/../config/global.config.inc.php');

/**
 * Used to export UDF in MS Excel format
 */
class UDF extends basis_db
{
	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Gets the titles (short description) of the UDF related to the table tbl_person
	 */
	public function getTitlesPerson()
	{
		return $this->_loadTitles($this->_getUDFDefinition($this->loadPersonJsons()));
	}
	
	/**
	 * Gets the titles (short description) of the UDF related to the table tbl_prestudent
	 */
	public function getTitlesPrestudent()
	{
		return $this->_loadTitles($this->_getUDFDefinition($this->loadPrestudentJsons()));
	}
	
	/**
	 * Loads the UDF definitions related to the table tbl_person
	 */
	public function loadPersonJsons()
	{
		$jsons = null;
		
		if ($this->existsUDF() && $this->prestudentHasUDF())
		{
			$jsons = $this->_loadJsons('public', 'tbl_person');
		}
		
		return $jsons;
	}
	
	/**
	 * Loads the UDF definitions related to the table tbl_prestudent
	 */
	public function loadPrestudentJsons()
	{
		$jsons = null;
		
		if ($this->existsUDF() && $this->prestudentHasUDF())
		{
			$jsons = $this->_loadJsons('public', 'tbl_prestudent');
		}
		
		return $jsons;
	}
	
	/**
	 * Checks if the table system.tbl_udf exists
	 */
	public function existsUDF()
	{
		$existsUDF = false;
		
		$query = 'SELECT COUNT(*) AS count
					FROM information_schema.columns
				   WHERE table_schema = \'system\'
				     AND table_name = \'tbl_udf\'';
		
		if (!$this->db_query($query))
		{
			$this->errormsg = 'Error!!!';
		}
		else
		{
			if ($row = $this->db_fetch_object())
			{
				if (isset($row->count) && $row->count > 0)
				{
					$existsUDF = true;
				}
			}
		}
		
		return $existsUDF;
	}
	
	/**
	 * Checks if the column udf_values exists in table tbl_person
	 */
	public function personHasUDF()
	{
		$personHasUDF = false;
		
		$query = 'SELECT COUNT(*) AS count
					FROM information_schema.columns
				   WHERE table_schema = \'public\'
				     AND table_name = \'tbl_person\'
				     AND column_name = \'udf_values\'';
		
		if (!$this->db_query($query))
		{
			$this->errormsg = 'Error!!!';
		}
		else
		{
			if ($row = $this->db_fetch_object())
			{
				if (isset($row->count) && $row->count > 0)
				{
					$personHasUDF = true;
				}
			}
		}
		
		return $personHasUDF;
	}
	
	/**
	 * Checks if the column udf_values exists in table tbl_prestudent
	 */
	public function prestudentHasUDF()
	{
		$prestudentHasUDF = false;
		
		$query = 'SELECT COUNT(*) AS count
					FROM information_schema.columns
				   WHERE table_schema = \'public\'
				     AND table_name = \'tbl_prestudent\'
				     AND column_name = \'udf_values\'';
		
		if (!$this->db_query($query))
		{
			$this->errormsg = 'Error!!!';
		}
		else
		{
			if ($row = $this->db_fetch_object())
			{
				if (isset($row->count) && $row->count > 0)
				{
					$prestudentHasUDF = true;
				}
			}
		}
		
		return $prestudentHasUDF;
	}
	
	/**
	 * Loads the UDF definitions related to the given schema and table
	 */
	private function _loadJsons($schema, $table)
	{
		$jsons = null;
		$query = 'SELECT jsons FROM system.tbl_udf WHERE schema = \''.$schema.'\' AND "table" = \''.$table.'\'';
		
		if (!$this->db_query($query))
		{
			$this->errormsg = 'Error occurred while loading jsons';
		}
		else
		{
			if ($row = $this->db_fetch_object())
			{
				$jsons = $row->jsons;
			}
		}
		
		return $jsons;
	}
	
	/**
     * Sorts the UDF definitions using the proprierty "sort"
     */
    private function _sortJsonSchemas(&$jsonSchemasArray)
    {
		usort($jsonSchemasArray, function ($a, $b) {
			// 
			if (!isset($a->sort))
			{
				$a->sort = 9999;
			}
			if (!isset($b->sort))
			{
				$b->sort = 9999;
			}
			
			if ($a->sort == $b->sort)
			{
				return 0;
			}
			
			return ($a->sort < $b->sort) ? -1 : 1;
		});
    }
    
    /**
     * Returns an array of associative arrays that contains the couple name and title related to an UDF
     * These data are retrived from the UDF definitions given as parameter
     */
    private function _getUDFDefinition($jsons)
    {
		$names = array();
		
		if ($jsons != null && ($jsonsDecoded = json_decode($jsons)) != null)
		{
			if (is_object($jsonsDecoded) || is_array($jsonsDecoded))
			{
				if (is_object($jsonsDecoded))
				{
					$jsonsDecoded = array($jsonsDecoded);
				}
				
				$this->_sortJsonSchemas($jsonsDecoded);
				
				foreach($jsonsDecoded as $udfJsonShema)
				{
					if (isset($udfJsonShema->name) && isset($udfJsonShema->title))
					{
						$names[] = array('name' => $udfJsonShema->name, 'title' => $udfJsonShema->title);
					}
				}
			}
		}
		
		return $names;
    }
    
    /**
     * Loads UDf titles from phrases
     */
	private function _loadTitles($udfDefinitions)
	{
		$titles = array();
		$in = '';
		
		for($i = 0; $i < count($udfDefinitions); $i++)
		{
			$udfDefinition = $udfDefinitions[$i];
			$in .= '\''.$udfDefinition['title'].'\'';
			
			if ($i < count($udfDefinitions) - 1) $in .= ', ';
		}
		
		if ($in != '')
		{
			$query = 'SELECT pt.text AS title, p.phrase AS phrase
							FROM system.tbl_phrase p INNER JOIN system.tbl_phrasentext pt USING(phrase_id)
						WHERE pt.sprache = \''.DEFAULT_LEHREINHEIT_SPRACHE.'\'
							AND p.phrase IN ('.$in.')';
				
			if (!$this->db_query($query))
			{
				$this->errormsg = 'Error occurred while loading jsons';
			}
			else
			{
				while ($row = $this->db_fetch_assoc())
				{
					for($i = 0; $i < count($udfDefinitions); $i++)
					{
						$udfDefinition = $udfDefinitions[$i];
						if ($udfDefinition['title'] == $row['phrase'])
						{
							$udfDefinition['description'] = $row['title'];
							$titles[] = $udfDefinition;
						}
					}
				}
			}
		}
		
		return $titles;
	}
}