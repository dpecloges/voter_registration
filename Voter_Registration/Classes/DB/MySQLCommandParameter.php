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

class MySQLCommandParameter
{


    public $Index;
    public $Value;
    public $Type;
    public $Length;

    public function __construct($paramIndex, $value, $type, $length = NULL)
    {
        $this->Index = $paramIndex;
        $this->Value = $value;
        $this->Type = $type;
        $this->Length = $length;
    }

}

?>
