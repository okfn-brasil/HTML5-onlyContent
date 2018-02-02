<?php
/**
 * Converts STDIN or URI (from good XHTML5) into "pretty XHTML5". Standard for digital preservation and checksum.
 * Examples of use in terminal (CLI) mode, at the root of the project:
 *   php src/prettyHTML5.php  < test.htm | more
 */

$VERBOSE_ERRORS = true;
if ($argc<2) 
	$url = 'php://stdin';
else 
	$url = $argv[1];
if (!$url) 
	die("\nERROR: without defined input\n");


fwrite( STDERR, "\n--- xml-tweezers representation ---\n Use php prog < file\n" );

$xt = new xml_tweezers( file_get_contents($url) );
$xt->pretty_lex();
echo $xt->asXML();


//////////////////// LIB


/**
 * XML-Tweezers tool kit, to normalize spaces and separators in complex XML structures like HTML5 or JATS.
 * Offers a JSON+UTF8 alternative representation of XML, for use in regular expression analysis.
 * Can be used with XML-fragments after XPath choice.
 * Conventions: prepared for HTML5-onlyContent, see https://github.com/okfn-brasil/HTML5-onlyContent 
 */
class xml_tweezers {

	var $xSource = '';
	var $xContainer = NULL;

	function __construct($xml='') {
		if ($xml) return $this->getSource($xml);
	}

	function getSource($xml='') { // constructor or refresh
		$this->xContainer = [
			'C14N'    => false, // true when was filtered before.
			'trimAtt' => false,
			'preserve'=> ['pre','script','style'],
			'blkTags' => [
				'html','base','body','head','title','meta','link',
				'address','blockquote','article','aside','div','footer','header','main','section',
				'table','tbody','tfoot','thead','tr','nav','caption','figure','figcaption','h1','h2','h3','h4','h5','h6',
				'col','colgroup','dd','dl','dt','img','li','ol','ul','p','svg','td','th'
			],  //NULL, // block-tags as div.
			'open'  =>[], 'close' =>[], 'attr'  =>[],
			'xCore' =>'',
			'opct'=>0, 'clct'=>0  //counterns
		];

		if ($xml) {
			$this->xSource = $xml;
			$this->C14N(true);
		} elseif (!$this->xSource) 
			die("\nERRO: tentando usar sem carga de xSource\n");
		$pt = &$this->xContainer;
		$BTAGS = (count($pt['blkTags'])>0);
		$xml_core = preg_replace_callback(
			'#<(\s*/\s*)?(\w+)(\s[^>]+)?>#su',
			function ($m) use (&$pt,$BTAGS) {
				if (isset($m[1]) && $m[1]>'') {
					$pt['close'][] = $m[2];
					return ($BTAGS && in_array($m[2],$pt['blkTags']))? '': '';
				} else {
					$pt['open'][] = $m[2];
					$pt['attr'][] = isset($m[3])? ($pt['trimAtt']? trim($m[3]):$m[3]): NULL;
					return ($BTAGS && in_array($m[2],$pt['blkTags']))? '': '';
				}
			},
			$this->xSource
		);
		$this->xContainer['xCore'] = $xml_core;
		return true;
	}


	function C14N($enforce=false) {
		if (!$enforce && isset($this->xContainer['C14N']) && $this->xContainer['C14N']) 
			return true;
		else {
			$dom = new DOMDocument;
			$len = mb_strlen($this->xSource,'UTF-8');
			if ($len<2) 
				die("\nSTRANGE length for an XML string\n");
			$dom->loadXML( $this->xSource ); // any XHTML is valid XML
			$dom->normalizeDocument();   // secondary DOM normalization
			$xml = $dom->documentElement->C14N(); // main normalization
			$len2 = mb_strlen($xml,'UTF-8');
			if ($len2<2 || $len2>(2*$len)) 
				die("\nSTRANGE length for an C14N string ($len2 vs $len)\n");
			$this->xSource=$xml;
			$this->xContainer['C14N']=true;
			return true;
		}
	}

	function asJSON($field='') {
		return json_encode(
			$field? $this->xContainer[$field] :$this->xContainer,
			JSON_UNESCAPED_UNICODE  // CRLF?  precisa ficar somente \n nao \r
		);
	}

	function isEqual() {
		return $this->asXML() == $this->xSource;
	}

	function asXML() {
		$this->xContainer['opct'] = 0; // open-tag counter
		$this->xContainer['clct'] = 0; // close-tag counter
		$pt = &$this->xContainer;
		return preg_replace_callback(
			'#([])#su',
			function ($m) use (&$pt) {
				if ($m[1]=='' || $m[1]=='') { // open
					$tmp = $pt['open'][$pt['opct']];
					if ($pt['attr'][$pt['opct']]) $tmp.= ($pt['trimAtt']? ' ': '').$pt['attr'][$pt['opct']];
					$pt['opct']++;
					return "<$tmp>";
				} else {
					$tmp = $pt['close'][$pt['clct']];
					$pt['clct']++;
					return "</$tmp>";
				}
			},
			$this->xContainer['xCore']
		);
	}


	function asXML_v2() { // ~4x faster! but bug, check sequence-align in little XML
		// times: 0.0008 vs 0.0011
		$open = [];
		for($i=0; $i<count($this->xContainer['open']); $i++)
			$open[$i] = '<'.$this->xContainer['open'][$i]
				.( $this->xContainer['trimAtt']? ' ': '' )
				.$this->xContainer['attr'][$i].'>';
		$N=$i;
		$XML = '';
		$xmls = explode('',$this->xContainer['xCore']); // explode by open-tags
		for($i=0; $i<count($xmls); $i++)
			$XML .= (isset($open[$i])? $open[$i]: '??open?').$xmls[$i];
		$xmls = explode('',$XML); // explode by close-tags
		$XML = $xmls[0];
		for($i=1; $i<count($xmls); $i++) {
			$c = isset($this->xContainer['close'][$i])? ('</'.$this->xContainer['close'][$i-1].'>'): '??close?';
			$XML .= $xmls[$i].$c;
		}
		return $XML;
	}




	function pretty_lex() {
		// filters for https://github.com/okfn-brasil/lexml-dou
		// esconder as tags PRE em uma token numerada... e devolver as tokens no final.
		$xml = $this->xContainer['xCore'];
		$xml = preg_replace( '#[\n\s\r]+#us',  ' ',	$xml  ); // remove spaces and all CR and CRLF
		$xml = preg_replace( '# *#us',        "\n", 	$xml  );
		$xml = preg_replace( '# +#us',        "", 	$xml  );

		$xml = preg_replace( '#―#us',        ' - ', 	$xml  ); // space for "dash by intention", is not hyphen
		$xml = preg_replace( '#[‒–—―]#us',   '-', 	$xml  ); // all are hyphens 
		$xml = preg_replace( '# ?\- ?#us',  " – ", 	$xml  ); // minor separator, use "n", &ndash;
		$xml = preg_replace( '# +\- +#us',   " — ",  	$xml  ); // in fact a dash, normalize translating to "M", &mdash;

		$xml = preg_replace( '# +#us',  "\n", 	$xml  ); // open
		$xml = preg_replace( '# +#us',  "\n", 	$xml  ); // close

		$xml = trim( pretty_parentheses($xml, '', "   ", '', '') )."\n"; // main transformation.

		$this->xContainer['xCore'] = $xml;
	}

} // class



/**
 * Parses any hierarchy of open/close characteres like parentheses.
 * The "prettyness" is to break and indent lines.
 * See https://stackoverflow.com/a/48581709
 */
function pretty_parentheses($txt, $sep='', $sepStep="\t", $open='(', $close=')') {
	$oc = $open.$close;
	$kenelRegex = "#\\$open(((?>[^$oc]+)|(?R))*)\\$close#su";
	//old $kenelRegex = "#\\$open(   ( (? >[^$oc]+) | (?R) )*  )\\$close#sux";
	return preg_replace_callback(
		$kenelRegex,
		function ($m) use ($sep, $sepStep, $open, $close) {
		    $r = trim($m[1]);
		    if (mb_strpos($r,$open,0,'UTF-8') === false)
		      return "\n$sep$open{$r}$close";
		    else 
		      return "\n$sep$open".pretty_parentheses($r,"$sepStep$sep",$sepStep, $open,$close)."\n$sep$close";
		},
		$txt
	);
}



