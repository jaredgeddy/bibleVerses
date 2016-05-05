<?php
/**
 * Plugin Name: Jared's Bible Verses
 * Description: A plugin that easily allows you to quote Bible verses in your posts
 * Version: 1.0
 * Author: Jared Eddy
 * Author URI: http://www.sitesrealized.com
 */
 

 function includeStyles()
{
    
    wp_enqueue_style( 'Bible Verses Style', plugins_url( 'assets/css/bibleVerses.css', __FILE__ ), 'all' );
}
add_action( 'wp_enqueue_scripts', 'includeStyles' );

class bibleVerses {


	public function __construct() {
        add_shortcode('verse', array($this, 'bibleVerses'));
    }
     
    public function bibleVerses(){
        $output = "";

        $verse = $this->getVerse();
        $output = "<div class=\"bible-verse\">".$verse['quote']."</div>";
        $output .= "<div class=\"bible-verse-details\">".$verse['book']." ".$verse['chapter'].":". $verse['verses']." ".$verse['version']."</div>";
        
        return $output;
    }

   	function getVerse() {
   		$verses = array (
   			array (
	   			"quote"=>"For God so loved the world that he gave his one and only Son, that whoever believes in him shall not perish but have eternal life.",
	   			"book"=>"John",
	   			"chapter"=>"3",
	   			"verses"=>"16",
	   			"version"=>"NIV"
	   			),
   			array (
	   			"quote"=>"Get rid of all bitterness, rage and anger, brawling and slander, along with every form of malice. 32 Be kind and compassionate to one another, forgiving each other, just as in Christ God forgave you.",
	   			"book"=>"Ephesians",
	   			"chapter"=>"4",
	   			"verses"=>"31-32",
	   			"version"=>"NIV"
	   			),
   			array (
	   			"quote"=>"Therefore do not worry about tomorrow, for tomorrow will worry about itself. Each day has enough trouble of its own.",
	   			"book"=>"Matthew",
	   			"chapter"=>"6",
	   			"verses"=>"34",
	   			"version"=>"NIV"
	   			)
   		);
   		return $verses[array_rand($verses,1)];
   	}
};


$bibleVerses = new bibleVerses ();