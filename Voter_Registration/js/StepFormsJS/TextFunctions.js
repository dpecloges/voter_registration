function FixTextInputToUpperCaseGreek(textInput)
{
	var s = textInput.val();
	s = s.includes(" ") ? s.replace(" ","") : s;
	s = s.toUpperCase();
	s = ConvertStringToUppercaseGreek(s);
	s = RemoveCharactersThatAreNotCapitalGreek(s);
	textInput.val(s);	
}

function FixTextInputToUpperCaseGreekWithSpaces(textInput)
{
	var s = textInput.val();
	//s = s.includes(" ") ? s.replace(" ","") : s;
	s = s.toUpperCase();
	s = ConvertStringToUppercaseGreek(s);
	
	
	s =s.replace('/[^Α-Ω_ -]/','');
	
	//s = RemoveCharactersThatAreNotCapitalGreek(s);
	textInput.val(s);	
}


function FixTextInputToNumbersOnly(textInput)
{
	var s = textInput.val();
	s = s.includes(" ") ? s.replace(" ","") : s;
	s = s.toUpperCase();
	s = RemoveCharactersThatAreNotNumbers(s);
	textInput.val(s);	
}


function ConvertStringToUppercaseGreek(str)
{
	str = str.toUpperCase();
	var upperEnglish = ['A', 'B', 'G', 'D', 'E', 'Z', 'H', 'U', 'I', 'K', 'L', 'M', 'N', 'J', 'O', 'P', 'R', 'S', 'T', 'Y', 'F', 'X', 'C', 'V', 'Y', 'Z', 'W'];
	var upperGreek =   ['Α', 'Β', 'Γ', 'Δ', 'Ε', 'Ζ', 'Η', 'Θ', 'Ι', 'Κ', 'Λ', 'Μ', 'Ν', 'Ξ', 'Ο', 'Π', 'Ρ', 'Σ', 'Τ', 'Υ', 'Φ', 'Χ', 'Ψ', 'Ω', 'Υ', 'Ζ', 'Σ'];
	var str = ArrayCharactersReplace (upperEnglish , upperGreek , str);
	return str;
}

function RemoveCharactersThatAreNotCapitalGreek(str)
{
	str = str.replace(/[^Α-Ω]/gi,'');	
	return str;		
}

function RemoveCharactersThatAreNotNumbers(str)
{
	str = str.replace(/[^0-9]/gi,'');	
	return str;		
}


function ArrayCharactersReplace (search, replace, subject, countObj) { 
	  var i = 0
	  var j = 0
	  var temp = ''
	  var repl = ''
	  var sl = 0
	  var fl = 0
	  var f = [].concat(search)
	  var r = [].concat(replace)
	  var s = subject
	  var ra = Object.prototype.toString.call(r) === '[object Array]'
	  var sa = Object.prototype.toString.call(s) === '[object Array]'
	  s = [].concat(s)
	  var $global = (typeof window !== 'undefined' ? window : global)
	  $global.$locutus = $global.$locutus || {}
	  var $locutus = $global.$locutus
	  $locutus.php = $locutus.php || {}
	  if (typeof (search) === 'object' && typeof (replace) === 'string') {
	    temp = replace
	    replace = []
	    for (i = 0; i < search.length; i += 1) {
	      replace[i] = temp
	    }
	    temp = ''
	    r = [].concat(replace)
	    ra = Object.prototype.toString.call(r) === '[object Array]'
	  }
	  if (typeof countObj !== 'undefined') {
	    countObj.value = 0
	  }
	  for (i = 0, sl = s.length; i < sl; i++) {
	    if (s[i] === '') {
	      continue
	    }
	    for (j = 0, fl = f.length; j < fl; j++) {
	      temp = s[i] + ''
	      repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0]
	      s[i] = (temp).split(f[j]).join(repl)
	      if (typeof countObj !== 'undefined') {
	        countObj.value += ((temp.split(f[j])).length - 1)
	      }
	    }
	  }
	  return sa ? s : s[0]
	}
