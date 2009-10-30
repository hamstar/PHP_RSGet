<?php

	/**
	* RapidShare File Checker API Wrapper
	*
	* API Wrapper for the checkfiles subroutine.  Takes a string
	* or array of urls, checks their status and returns a url to the
	* file if they are downloadable.
	*
	* Author: Robert McLeod
	* Copyright: 2009 Robert McLeod
	* Licence: http://creativecommons.org/licenses/by-nc-sa/3.0/nz/
	* Link: http://creativecommons.org/licenses/by-nc-sa/3.0/nz/
	* Version: 0.1b
	*
	**/

	/** Main Class **/
	class PHP_RSGet {
	
		/*
		* Set the status messages up
		*/
		private $sMessages = array(
			0 => 'File not found',
			1 => 'File OK (Downloading possible without any logging)',
			2 => 'File OK (TrafficShare direct download without any logging)',
			3 => 'Server down',
			4 => 'File marked as illegal',
			5 => 'Anonymous file locked, because it has more than 10 downloads already',
			6 => 'File OK (TrafficShare direct download with enabled logging. Read our privacy policy to see what is logged.)'
		);

		/**
		* Empty constructor
		*/
		function __construct() {
		
		}
		
		/**
		* This function builds the query once all urls are
		* processed.
		*
		* @return void
		*/
		private function buildQuery() {
			
			// Quickly implode the files into an array argument
			$this->q = 'files=' . implode( ',', $this->fids );
			$this->q .= '&filenames=' . implode( ',', $this->fns );
		
		}
		
		/**
		* This function builds the urls for each link checked
		* and puts it back into the array.
		*
		* @return void
		*/
		private function buildUrls() {
		
			// Run through each link to get the data
			// for building the url
			foreach ( $this->linkData as &$ld ) {
				
				// Check that we have a file
				if ( $ld[2] > 0 ) {
				
					// Build the url from the various components
					$ld['url'] = 'http://rs' . $ld[3] . $ld[5] . '.rapidshare.com/files/'
						. $ld[0] . '/' . $ld[1];
						
				} else {
				
					// Set the url to null
					$ld['url'] = null;
					
				}			
			}
		
		}
		
		/**
		* This is the function to run the api call
		* that gets the data on the links.
		*
		* @return void
		*/
		private function runQuery() {
		
			// Build the query
			$this->buildQuery();
			
			// Set the url for the call
			$url = 'https://api.rapidshare.com/cgi-bin/rsapi.cgi?sub=checkfiles_v1&' . $this->q;
			
			// Start curl
			$c = new Curl;
			
			// Get the url data
			$csv = $c->get( $url );
			
			// If the call succeeded...
			if ( $csv->headers['Status-Code'] == 200 ) {
			
				// Split up the return by newline
				foreach ( explode( "\n", trim( $csv ) ) as $line ) {
			
					// Explode the link data into an array
					$this->linkData[] = explode( ',', $line );
				
				}
			
			}
			
			// Build the URLs
			$this->buildUrls();
		
		}
		
		/**
		* This is the function to turn the arrays into objects
		* for returning.
		*
		* @return array
		*/
		private function objectify() {
		
			// Run through each linkData
			foreach ( $this->linkData as $ld ) {
			
				// Create a standard object
				$l = new StdClass;
				
				// Set each parameter in the object
				$l->link = $ld['url'];
				$l->fid = $ld[0];
				$l->fn = $ld[1];
				$l->size = $ld[2];
				$l->server = $ld[3];
				$l->status_code = $ld[4];
				$l->status_msg = $this->sMessages[ $ld[4] ];
				$l->shorthost = $ld[5];
				$l->md5 = $ld[6];
				
				// Throw the object into this array
				$objects[] = $l;
				
				// Unset the object
				$l = null;
			
			}
			
			// Return the array of objects
			return $objects;
		
		}
		
		/**
		* This is the function that does it all
		*
		* @param mixed $urls The array or string of the url
		* @return array
		*/
		public function getLinks($urls) {
		
			// If it is a string make it an array
			if ( is_string($urls) ) {
			
				$url = $urls;
				$urls = array();
				$urls[] = $url;
			
			}
			
			// Run through each url
			foreach ( $urls as $url ) {
			
				// Run a preg_match to get the file id and name
				if ( preg_match( '#/(?<fid>\d+)/(?<fn>.+)$#', $url, $m ) ) {
			
					// Save the data
					$this->fids[] = $m['fid'];
					$this->fns[] =  $m['fn'];
					
				} else {
					// Some kind of fail message here
					// Not proper RS link
				}
			
			}
			
			// Run the query
			$this->runQuery();
			
			// Return the objects in an array
			return $this->objectify();
		
		}
	
	}
