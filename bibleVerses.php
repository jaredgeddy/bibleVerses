<?php
/**
 * Plugin Name: Jared's Bible Verses
 * Description: A plugin that easily allows you to quote Bible verses in your posts
 * Version: 1.0
 * Author: Jared Eddy
 * Author URI: http://www.sitesrealized.com
 */
 
class bibleVerses {

	public function __construct() {
        add_shortcode('verse', array($this, 'bibleVerses'));
        add_action( 'wp_enqueue_scripts', array($this, 'includeStyles'));

		if (array_key_exists('jbvtest', $_GET) && ($_GET['jbvtest'] == '1')) {
			add_action('init', array($this,'test'), 12);
		}

    }
    
    public function test() {

    	echo '<pre>';
    	echo 'Creating tables....<br />';
    	$this->createVersionTable();
    	echo 'Created tables....<br />';
    	echo 'Truncating data we already had....<br />';
    	$this->truncateTables();
    	echo 'Finished truncating....<br />';
    	echo 'Load tables from remote....<br />';
    	$this->loadTablesFromRemote();
    	echo 'Finished loading tables....<br />';
    	echo 'Done.<br />';
    	echo '</pre>';

    	wp_die();

    }

	public function includeStyles() {
		wp_enqueue_style( 'Bible Verses Style', plugins_url( 'assets/css/bibleVerses.css', __FILE__ ), 'all' );
	}

    public function bibleVerses(){
        $output = "";

        $verse = $this->getVerse();
        $output = '<div class="bible-verse">'.$verse['quote'].'</div>';
        $output .= '<div class="bible-verse-details">'.$verse['book'].' '.$verse['chapter'].':'. $verse['verses'].' '.$verse['version'].'</div>';
        
        return $output;
    }

   	private function getVerse() {
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

   	// Creates empty tables
   	private function createVersionTable() {
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		// bible_version_key table creation		
		$table_name = $wpdb->prefix . 'bible_version_key';
		
		$sql = "CREATE TABLE `{$table_name}` (
			  `id` int(3) unsigned zerofill NOT NULL AUTO_INCREMENT,
			  `table` text NOT NULL COMMENT 'Database Table Name ',
			  `abbreviation` text NOT NULL COMMENT 'Version Abbreviation',
			  `language` text NOT NULL COMMENT 'Language of bible translation (used for language key tables)',
			  `version` text NOT NULL COMMENT 'Version Name',
			  `info_text` text NOT NULL COMMENT 'About / Info',
			  `info_url` text NOT NULL COMMENT 'Info URL',
			  `publisher` text NOT NULL COMMENT 'Publisher',
			  `copyright` text NOT NULL COMMENT 'Copyright ',
			  `copyright_info` text NOT NULL COMMENT 'Extended Copyright info',
			  PRIMARY KEY (`id`)
			);";
		
		dbDelta($sql);

		$table_name = $wpdb->prefix . 'key_abbreviations_english';

		$sql = "CREATE TABLE `{$table_name}` (
			  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Abbreviation ID',
  			  `a` varchar(255) NOT NULL,
  			  `b` smallint(5) unsigned NOT NULL COMMENT 'ID of book that is abbreviated',
  			  `p` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether an abbreviation is the primary one for the book',
  			  PRIMARY KEY (`id`)
  			  );";
		
		dbDelta($sql);
   	}

   	// Empties data from the tables without deleting the tables themselves
   	private function truncateTables() {
   		global $wpdb;

   		$wpdb->query('truncate table '.$wpdb->prefix.'bible_version_key');
   		$wpdb->query('truncate table '.$wpdb->prefix.'key_abbreviations_english');
   	}

   	// Get the data to put into the tables from CSV files on GitHub
   	private function loadTablesFromRemote() {

   		global $wpdb;
   		$origin = 'https://raw.githubusercontent.com/scrollmapper/bible_databases/master/csv/';

        // Fix for CSVs from OSX
        ini_set("auto_detect_line_endings", "1");   

   		// Load bible_version_key table
   		// Get the remote CSV file
   		$filename = 'bible_version_key.csv';
        $remote = $origin.$filename;
        file_put_contents($filename,fopen($remote,'r'));


        // Open the local file we just acquired
		$file = fopen($filename,'r');

        // Skip first row, return if we don't have that
        if (($line = fgetcsv($file)) == FALSE) {
        	fclose($file);
			return;
        }

        // Now process lines
        while (($line = fgetcsv($file)) !== FALSE) {
	        $wpdb->insert($wpdb->prefix.'bible_version_key', array(
	        	'id' => $line[0],
	        	'table' => $line[1],
	        	'abbreviation' => $line[2],
	        	'language' => $line[3],
	        	'version' => $line[4],
	        	'info_text' => $line[5],
	        	'info_url' => $line[6],
	        	'publisher' => $line[7],
	        	'copyright' => $line[8],
	        	'copyright_info' => $line[9]
	        ));
        }

        fclose($file);

         // Load key_abbreviations_english table
   		// Get the remote CSV file
   		$filename = 'key_abbreviations_english.csv';
        $remote = $origin.$filename;
        file_put_contents($filename,fopen($remote,'r'));


        // Open the local file we just acquired
		$file = fopen($filename,'r');

        // Skip first row, return if we don't have that
        if (($line = fgetcsv($file)) == FALSE) {
        	fclose($file);
			return;
        }

        // Now process lines
        while (($line = fgetcsv($file)) !== FALSE) {
	        $wpdb->insert($wpdb->prefix.'key_abbreviations_english', array(
	        	'id' => $line[0],
	        	'a' => $line[1],
	        	'b' => $line[2],
	        	'p' => $line[3]
	        ));
        }

        fclose($file);
   	}


   	// Remove the tables entirely from the database
   	private function deleteTables() {

   	}

};


$bibleVerses = new bibleVerses();
