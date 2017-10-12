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

class MySQLConnection
{

    private $fLink;    // The mysqli link with the selected MySQL database

    /**
     * Constructs a new MySQLConnection
     * @param string $serverIP is the mySQL's server IP
     * @param string $database is the database to connect
     * @param string $user is a user with permissions for the selected database
     * @param string $password is the user's password
     * @param string $charset is the the database charset
     */

    public function __construct($serverIP, $database, $user, $password, $charset="")
    {
 		$this->fLink = new mysqli($serverIP, $user, $password, $database);
		//$this->fLink->ssl_set("/var/www/registration/Classes/DB/client-key.pem","/var/www/registration/Classes/DB/client-cert.pem","/var/www/registration/Classes/DB/server-ca.pem",NULL,NULL); 
 		

        if ($this->fLink->connect_errno)
        {
            throw new Exception("Failed to connect to MySQL: (" . $this->fLink->connect_errno . ") " . $this->fLink->connect_error);
        }

        if ($charset != "")
        {
            $this->fLink->set_charset($charset);
        }
    }
    
   
    /**
     * Escapes special characters in a string for use in an SQL statement
     * @param string $string is the string to escape
     * @return string string
     */
    public function EscapeString($string)
    {
        return mysql_real_escape_string($string, $this->fLink);
    }

    /**
     * Close MySQLConnection
     */
    public function Close()
    {
        $this->fLink->close();
    }

    /**
     * Returns the mysqli link with selected MySQL database.
     * @return mysqli 
     */
    public function getLink()
    {
        return $this->fLink;
    }
}
?>