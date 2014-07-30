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

class SyntaxHighlighter {

	public function config_loaded(&$settings)
	{	
		$this->theme_url = $settings['base_url'] .'/'. basename(THEMES_DIR) .'/'. $settings['theme'];
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
    }

    public function file_meta(&$meta)
    {	
    	$exclude = "";
    	if (isset($this->synhigh_exclude))
    	{
    		foreach ($this->synhigh_exclude as $metaname => $metavalue)
    		{
	        	$metavalue = explode('|', $metavalue);
	        	foreach ($metavalue as $key => $value) {
	        		if (isset($meta[$metaname]) && $meta[$metaname] == $value)
	        		{
	        			$exclude = true;
	        		}
	        	}
    		}
    	}
    	
    	$this->exclude = $exclude;
    
    	if (empty($this->exclude))
    	{
	        if (isset($this->synhigh_autoloader) && $this->synhigh_autoloader === true)
	        {
	    		$this->synhigh_loader = '<script src="'.$this->theme_url.'/scripts/syntaxhighlighter/scripts/shAutoloader.js"></script>';
	    		if (isset($this->synhigh_autobrush) && is_array($this->synhigh_autobrush))
	    		{
	    			$synhigh_autobrushscripts = '';
	    			foreach ($this->synhigh_autobrush as $key => $value)
		            {
		            	$synhigh_autobrushscripts .= '\''.$key.'		'.$this->theme_url.'/scripts/syntaxhighlighter/scripts/shBrush'.$value.'.js\','.PHP_EOL;
		            }
		            $this->synhigh_autobrushscripts = rtrim($synhigh_autobrushscripts, ",".PHP_EOL);
	    		}
	        }
	    	elseif((isset($this->synhigh_autoloader) && $this->synhigh_autoloader === false) || empty($this->synhigh_autoloader))
	        {
	        	if (isset($this->synhigh_brush) && is_array($this->synhigh_brush))
		        {
		        	$synhigh_brushscripts = '';
		            foreach ($this->synhigh_brush as $key => $value)
		            {
		            	$synhigh_brushscripts .= '<script src="'.$this->theme_url.'/scripts/syntaxhighlighter/scripts/shBrush'.$value.'.js"></script>'.PHP_EOL;
		            }
		            $this->synhigh_loader = $synhigh_brushscripts.PHP_EOL.'<script>SyntaxHighlighter.all()</script>';
		        }
	        }
        }
    }

	public function after_render(&$output)
	{	
		if (isset($this->exclude) && $this->exclude != 1)
    	{
			if (isset($this->synhigh_autobrushscripts) && $this->synhigh_autobrushscripts != '') {
				$synhigh_body = '<script type="text/javascript">
						SyntaxHighlighter.autoloader('.PHP_EOL.
						$this->synhigh_autobrushscripts
						.PHP_EOL.');
						SyntaxHighlighter.all();
			    	</script>';
			    
		    	$output = str_replace('</body>', PHP_EOL.$synhigh_body.'</body>', $output);
			}

			$synhigh_head = '
		<link href="'.$this->theme_url.'/scripts/syntaxhighlighter/styles/shCore.css" rel="stylesheet" type="text/css" />
		<link href="'.$this->theme_url.'/scripts/syntaxhighlighter/styles/shTheme'.$this->synhigh_theme.'.css" rel="stylesheet" type="text/css" />
		<script src="'.$this->theme_url.'/scripts/syntaxhighlighter/scripts/shCore.js"></script>'.PHP_EOL.'	'.$this->synhigh_loader;

			$output = str_replace('</head>', PHP_EOL.$synhigh_head.'</head>', $output);
		}
	}
}

?>