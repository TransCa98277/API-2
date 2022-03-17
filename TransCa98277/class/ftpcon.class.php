<?php
class FTPCon {
	var $host = '';
	var $username = '';
	var $password = '';
	var $port = 21;
	var $timeout = 3;
	var $ftpcon = null;

	function __construct($host, $username, $password, $port = 21, $timeout = 3) {
		$this -> host = $host;
		$this -> username = $username;
		$this -> password = $password;
		$this -> port = $port;
		$this -> timeout = $timeout;
	}

	function connect() {
		if (isset($this -> host, $this -> username, $this -> password, $this -> port, $this -> timeout)) {
      		if ($this -> port == 21) {
    	 		$this -> ftpcon = ftp_connect($this -> host, $this -> port, $this -> timeout);
      		} else {
        		$this -> ftpcon = ftp_ssl_connect($this -> host, $this -> port, $this -> timeout);
      		}
      
			if (!$this -> ftpcon) {        
				return false;
			}

			if (!ftp_login($this -> ftpcon, $this -> username, $this -> password)) {
				return false;
			}
			ftp_pasv($this -> ftpcon, true);
			return true;
		}
	}

	function ls($dir = '.') {
		if (isset($this -> ftpcon)) {
			$ls = ftp_nlist($this -> ftpcon, $dir);
			return $ls;
		}
	}

	//$file : local, $path : remote
	function upload($file, $path, $mode = FTP_BINARY) {
		if (isset($file, $path)) {
			if (file_exists($file)) {
				$upload = ftp_put($this -> ftpcon, $path, $file, $mode);
				return $upload;
			}
		}
		return false;
	}

	//$file : remote, $path : local
	function download($file, $path, $mode = FTP_BINARY) {
		if (isset($file)) {
			if (ftp_get($this -> ftpcon, $path, $file, FTP_BINARY)) {
				return true;
			}
		}
		return false;
	}

	function chmod($file, $permissions = 0644) {
		if (isset($file)) {
			if (ftp_chmod($this -> ftpcon, $permissions, $file) !== false) {
				return true;
			} else {
				return false;
			}
		}
	}

	function mkdir($dirname) {
		if (isset($dirname)) {
			return ftp_mkdir($this -> ftpcon, $dirname);
		}
	}

	function chdir($dirname) {
		if (isset($dirname)) {
			return ftp_chdir($this -> ftpcon, $dirname);
		}
	}

	function getServerOS() {
		//Warnning : Cette fonction ne marche pas pour le systeme windows avec filezilla FTP server.  Elle voit filezilla FTP server comme UNIX.
		return ftp_systype($this -> ftpcon);
	}

	function getCurrentDir() {
		return ftp_pwd($this -> ftpcon);
	}

	function getFileSize($filename) {
		return ftp_size($this -> ftpcon, $filename);
	}

	function delete($file) {
		if (isset($file)) {
			return ftp_delete($this -> ftpcon, $file);
		}
	}

	function logout() {
		if (isset($this -> ftpcon)) {
			ftp_close($this -> ftpcon);
		}
	}

	function __destruct() {
		$this -> logout();
	}

}
?>