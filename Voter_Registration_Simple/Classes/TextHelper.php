<?php
class TextHelper
{

    public function __construct()
    {
    }
    
    
    public function CheckIfStringContainsOnlyGreekCapitalCharacters($str)
    {
    	$gr1 = array ('Α', 'Β', 'Γ', 'Δ', 'Ε', 'Ζ', 'Η', 'Θ', 'Ι', 'Κ', 'Λ', 'Μ','Ν', 'Ξ', 'Ο', 'Π', 'Ρ', 'Σ', 'Τ', 'Υ', 'Φ', 'Χ', 'Ψ', 'Ω');
		$gr2 = array ( '',  '',  '',  '',  '',  '',  '',  '',  '',  '',  '',  '','',  '',  '',  '',  '',  '',  '',  '',  '',  '',  '',  '');
		$str = str_replace($gr1, $gr2, $str);
		$r  = (strlen($str) > 0);
		return !$r;
    }
    
}
?>