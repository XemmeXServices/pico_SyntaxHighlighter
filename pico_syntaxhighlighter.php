<?php 
/**
 * Pico SyntaxHighlighter
 *
 * Add SyntaxHighlighter by Alex Gorbatchev
 * http://alexgorbatchev.com/SyntaxHighlighter/
 * to Pico
 *
 * @author Brice Boucard
 * @link https://github.com/bricebou/pico_SyntaxHighlighter
 * @license http://bricebou.mit-license.org/
 */

class Pico_SyntaxHighlighter {

	public function config_loaded(&$settings)
	{	
		/*	Get the SyntaxHighlighter plugin folder path */
        $dir = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
		$this->psh_url = $settings['base_url'] .'/'. basename(PLUGINS_DIR) .'/'.array_pop($dir);
		/*	Get the variables from the user's config.php */
		if (isset($settings['synhigh']['theme']))
        {
            $this->synhigh_theme = $settings['synhigh']['theme'];
        }
        if (isset($settings['synhigh']['autoloader']))
        {
        	$this->synhigh_autoloader = $settings['synhigh']['autoloader'];
        }
        if (isset($settings['synhigh']['autobrush']))
        {
        	$this->synhigh_autobrush = $settings['synhigh']['autobrush'];
        }
        if (isset($settings['synhigh']['brush']))
        {
        	$this->synhigh_brush = $settings['synhigh']['brush'];
        }
        if (isset($settings['synhigh']['exclude']))
        {
        	$this->synhigh_exclude = $settings['synhigh']['exclude'];
        }
        /*	Adding basic variables for SyntaxHighlighter */
        $this->synhigh_base = '
			<link href="'.$this->psh_url.'/syntaxhighlighter/styles/shCore.css" rel="stylesheet" type="text/css" />
			<link href="'.$this->psh_url.'/syntaxhighlighter/styles/shTheme'.$this->synhigh_theme.'.css" rel="stylesheet" type="text/css" />
			<script src="'.$this->psh_url.'/syntaxhighlighter/scripts/shCore.js"></script>';
		$this->synhigh_autoloaderscript = '<script src="'.$this->psh_url.'/syntaxhighlighter/scripts/shAutoloader.js"></script>';
    }

    /* 	Getting and inverting (for an easier comparison later)
    	the array of the current page meta 	*/
    public function file_meta(&$meta)
    {
    	$index;
        $clonemeta = $meta;
        /*  Removing array from the meta array in order to do an array_flip  */
        foreach ($clonemeta as $key => $value) {
            if (is_array($value)) {
                $index = $key;
                unset($clonemeta[$index]);
            }
        }
        $this->rmeta = array_flip(array_filter($clonemeta));
   	}

   	/*	Is the current page exluded based on user's configuration ?
   		return true if excluded, false if not excluded	*/
   	public function synhigh_test_exclude()
   	{
    	$metaexclude = array();
    	foreach ($this->synhigh_exclude as $metakey => $metavalue) {
    		/*	Split each line of the array
    			using the pipe | as separator	*/
    		$metavalue = explode('|', $metavalue);
    		/*	Creating an array with values as index
    			and meta names as values
    			(just like the array of the current page meta) */
    		foreach ($metavalue as $key => $value) {
    			$metaexclude[$value] = $metakey;
        	}
    	}
    	/*	Comparing the two arrays, keeping only matching pairs of key and value */
    	$this->diff = array_filter(array_intersect_assoc($metaexclude, $this->rmeta));
    	if (empty($this->diff)) {
    		return false;
    	}
    	else
    	{
    		return true;
    	}
    }

    public function build_scripts()
    {
    	/*	Building the scripts we have to add to each pages that are not excluded	*/
    	/*	If the autoloader feature is active	*/
        if (isset($this->synhigh_autoloader) && $this->synhigh_autoloader === true)
        {
    		if (isset($this->synhigh_autobrush) && is_array($this->synhigh_autobrush))
    		{
    			/*	Creating a string with all the brushes scripts that have to be loaded	*/
    			$synhigh_autobrushscripts = '';
    			foreach ($this->synhigh_autobrush as $key => $value)
	            {
	            	$synhigh_autobrushscripts .= '\''.$key.'		'.$this->psh_url.'/syntaxhighlighter/scripts/shBrush'.$value.'.js\','.PHP_EOL;
	            }
	            /*	Removing extra comma and space at the end of the last brush */
	            $this->synhigh_autobrushscripts = rtrim($synhigh_autobrushscripts, ",".PHP_EOL);
	            /*	Concatenating the brush inside a larger script
	            	that will be placed a the end of the page body 	*/
	            $this->synhigh_body = '<script type="text/javascript">
					SyntaxHighlighter.autoloader('.PHP_EOL.
					$this->synhigh_autobrushscripts.PHP_EOL.');
					SyntaxHighlighter.all();
		    	</script>';
		    	/*	Concatenating base and autoloader for the page head */
		    	$this->synhigh_head = $this->synhigh_base.PHP_EOL.$this->synhigh_autoloaderscript;
		    	/*	Returning body and head as an array
		    		for an easy access in after_render function */
		    	return array($this->synhigh_body, $this->synhigh_head);
    		}
        }
        /*	If the autoloader feature is not active or not present in the config.php 	*/
    	elseif((isset($this->synhigh_autoloader) && $this->synhigh_autoloader === false) || empty($this->synhigh_autoloader))
        {
        	if (isset($this->synhigh_brush) && is_array($this->synhigh_brush))
	        {
	        	/*	Creating a string with all the brushes scripts that have to be loaded	*/
	        	$synhigh_brushscripts = '';
	            foreach ($this->synhigh_brush as $key => $value)
	            {
	            	$synhigh_brushscripts .= '<script src="'.$this->psh_url.'/syntaxhighlighter/scripts/shBrush'.$value.'.js"></script>'.PHP_EOL;
	            }
	            /*	A single string for the page body 	*/
	            $this->synhigh_body = '<script>SyntaxHighlighter.all()</script>';
	            /*	Concatenating base and brushes scripts for the page head 	*/
	            $this->synhigh_head = $this->synhigh_base.PHP_EOL.$synhigh_brushscripts;
	            /*	Returning body and head as an array
		    		for an easy access in after_render function */
	            return array($this->synhigh_body, $this->synhigh_head);
	        }
        }
    }

    public function after_render(&$output)
    {	
    	/*	If the page is not excluded	*/
    	if ($this->synhigh_test_exclude() == false) {
    		/*	Modifying the page's end of body and head	*/
    		$outputtemp = $this->build_scripts();
    		$output = str_replace('</body>', PHP_EOL.$outputtemp[0].PHP_EOL.'</body>', $output);
    		$output = str_replace('</head>', PHP_EOL.$outputtemp[1].PHP_EOL.'</head>', $output);
    	}
    }
}
?>