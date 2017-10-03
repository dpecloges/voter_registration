<?php
/*
nCore PHP
Copyright (C) 2012  Nikos Siatras

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.*/


class MySQLCommandParameters
{

    private $fParameters;
    private $fMyCommand;

    public function __construct(MySQLCommand $myCommand)
    {
        $this->fParameters = array();
        $this->fMyCommand = $myCommand;
    }

    /**
     *
     * @param type $parameter
     * @param type $type 
     */
    private function Add($paramIndex, $value, $type, $length = NULL)
    {
        $parameter = new MySQLCommandParameter($paramIndex, $value, $type, $length);
        $this->fParameters[$paramIndex] = $parameter;
    }

    /**
     * Adds or sets a string parameter.
     * @param type $paramIndex
     * @param type $value
     * @param type $length 
     */
    public function setString($paramIndex, $value, $length = NULL)
    {
        $this->Add($paramIndex, $value, "s", $length);
    }

    /**
     * Adds or sets an integer parameter.
     * @param type $paramIndex
     * @param type $value
     * @param type $length 
     */
    public function setInteger($paramIndex, $value, $length = NULL)
    {
        $this->Add($paramIndex, $value, "i", $length);
    }

    /**
     * Adds or sets a double parameter.
     * @param type $paramIndex
     * @param type $value
     * @param type $length 
     */
    public function setDouble($paramIndex, $value, $length = NULL)
    {
        $this->Add($paramIndex, $value, "d", $length);
    }

    /**
     * Adds or sets a blob parameter.
     * @param type $paramIndex
     * @param type $value
     * @param type $length 
     */
    public function setBlob($paramIndex, $value, $length = NULL)
    {
        $this->Add($paramIndex, $value, "b", $length);
    }

    /**
     * Remove all parameters
     */
    public function Clear()
    {
        $this->fParameters = array();
    }

    public function getParameters()
    {
        return $this->fParameters;
    }

}

?>
