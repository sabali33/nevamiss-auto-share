<?php

declare(strict_types=1);


/**
 * Base WordPress Filesystem
 *
 * @package WordPress
 * @subpackage Filesystem
 */
/**
 * Base WordPress Filesystem class for which Filesystem implementations extend
 *
 * @since 2.5.0
 */
class WP_Filesystem_Base
{
	/**
	 * Whether to display debug data for the connection.
	 *
	 * @access public
	 * @since 2.5.0
	 * @var bool
	 */
	public $verbose = \false;
	/**
	 * Cached list of local filepaths to mapped remote filepaths.
	 *
	 * @access public
	 * @since 2.7.0
	 * @var array
	 */
	public $cache = array();
	/**
	 * The Access method of the current connection, Set automatically.
	 *
	 * @access public
	 * @since 2.5.0
	 * @var string
	 */
	public $method = '';
	/**
	 * @access public
	 */
	public $errors = \null;
	/**
	 * @access public
	 */
	public $options = array();
	/**
	 * Return the path on the remote filesystem of ABSPATH.
	 *
	 * @access public
	 * @since 2.7.0
	 *
	 * @return string The location of the remote path.
	 */
	public function abspath()
	{
	}
	/**
	 * Return the path on the remote filesystem of WP_CONTENT_DIR.
	 *
	 * @access public
	 * @since 2.7.0
	 *
	 * @return string The location of the remote path.
	 */
	public function wp_content_dir()
	{
	}
	/**
	 * Return the path on the remote filesystem of WP_PLUGIN_DIR.
	 *
	 * @access public
	 * @since 2.7.0
	 *
	 * @return string The location of the remote path.
	 */
	public function wp_plugins_dir()
	{
	}
	/**
	 * Return the path on the remote filesystem of the Themes Directory.
	 *
	 * @access public
	 * @since 2.7.0
	 *
	 * @param string $theme The Theme stylesheet or template for the directory.
	 * @return string The location of the remote path.
	 */
	public function wp_themes_dir($theme = \false)
	{
	}
	/**
	 * Return the path on the remote filesystem of WP_LANG_DIR.
	 *
	 * @access public
	 * @since 3.2.0
	 *
	 * @return string The location of the remote path.
	 */
	public function wp_lang_dir()
	{
	}
	/**
	 * Locate a folder on the remote filesystem.
	 *
	 * @access public
	 * @since 2.5.0
	 * @deprecated 2.7.0 use WP_Filesystem::abspath() or WP_Filesystem::wp_*_dir() instead.
	 * @see WP_Filesystem::abspath()
	 * @see WP_Filesystem::wp_content_dir()
	 * @see WP_Filesystem::wp_plugins_dir()
	 * @see WP_Filesystem::wp_themes_dir()
	 * @see WP_Filesystem::wp_lang_dir()
	 *
	 * @param string $base The folder to start searching from.
	 * @param bool   $echo True to display debug information.
	 *                     Default false.
	 * @return string The location of the remote path.
	 */
	public function find_base_dir($base = '.', $echo = \false)
	{
	}
	/**
	 * Locate a folder on the remote filesystem.
	 *
	 * @access public
	 * @since 2.5.0
	 * @deprecated 2.7.0 use WP_Filesystem::abspath() or WP_Filesystem::wp_*_dir() methods instead.
	 * @see WP_Filesystem::abspath()
	 * @see WP_Filesystem::wp_content_dir()
	 * @see WP_Filesystem::wp_plugins_dir()
	 * @see WP_Filesystem::wp_themes_dir()
	 * @see WP_Filesystem::wp_lang_dir()
	 *
	 * @param string $base The folder to start searching from.
	 * @param bool   $echo True to display debug information.
	 * @return string The location of the remote path.
	 */
	public function get_base_dir($base = '.', $echo = \false)
	{
	}
	/**
	 * Locate a folder on the remote filesystem.
	 *
	 * Assumes that on Windows systems, Stripping off the Drive
	 * letter is OK Sanitizes \\ to / in windows filepaths.
	 *
	 * @access public
	 * @since 2.7.0
	 *
	 * @param string $folder the folder to locate.
	 * @return string|false The location of the remote path, false on failure.
	 */
	public function find_folder($folder)
	{
	}
	/**
	 * Locate a folder on the remote filesystem.
	 *
	 * Expects Windows sanitized path.
	 *
	 * @access public
	 * @since 2.7.0
	 *
	 * @param string $folder The folder to locate.
	 * @param string $base   The folder to start searching from.
	 * @param bool   $loop   If the function has recursed, Internal use only.
	 * @return string|false The location of the remote path, false to cease looping.
	 */
	public function search_for_folder($folder, $base = '.', $loop = \false)
	{
	}
	/**
	 * Return the *nix-style file permissions for a file.
	 *
	 * From the PHP documentation page for fileperms().
	 *
	 * @link http://docs.php.net/fileperms
	 *
	 * @access public
	 * @since 2.5.0
	 *
	 * @param string $file String filename.
	 * @return string The *nix-style representation of permissions.
	 */
	public function gethchmod($file)
	{
	}
	/**
	 * Gets the permissions of the specified file or filepath in their octal format
	 *
	 * @access public
	 * @since 2.5.0
	 * @param string $file
	 * @return string the last 3 characters of the octal number
	 */
	public function getchmod($file)
	{
	}
	/**
	 * Convert *nix-style file permissions to a octal number.
	 *
	 * Converts '-rw-r--r--' to 0644
	 * From "info at rvgate dot nl"'s comment on the PHP documentation for chmod()
	 *
	 * @link http://docs.php.net/manual/en/function.chmod.php#49614
	 *
	 * @access public
	 * @since 2.5.0
	 *
	 * @param string $mode string The *nix-style file permission.
	 * @return int octal representation
	 */
	public function getnumchmodfromh($mode)
	{
	}
	/**
	 * Determine if the string provided contains binary characters.
	 *
	 * @access public
	 * @since 2.7.0
	 *
	 * @param string $text String to test against.
	 * @return bool true if string is binary, false otherwise.
	 */
	public function is_binary($text)
	{
	}
	/**
	 * Change the ownership of a file / folder.
	 *
	 * Default behavior is to do nothing, override this in your subclass, if desired.
	 *
	 * @access public
	 * @since 2.5.0
	 *
	 * @param string $file      Path to the file.
	 * @param mixed  $owner     A user name or number.
	 * @param bool   $recursive Optional. If set True changes file owner recursivly. Defaults to False.
	 * @return bool Returns true on success or false on failure.
	 */
	public function chown($file, $owner, $recursive = \false)
	{
	}
	/**
	 * Connect filesystem.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @return bool True on success or false on failure (always true for WP_Filesystem_Direct).
	 */
	public function connect()
	{
	}
	/**
	 * Read entire file into a string.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file Name of the file to read.
	 * @return mixed|bool Returns the read data or false on failure.
	 */
	public function get_contents($file)
	{
	}
	/**
	 * Read entire file into an array.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file Path to the file.
	 * @return array|bool the file contents in an array or false on failure.
	 */
	public function get_contents_array($file)
	{
	}
	/**
	 * Write a string to a file.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file     Remote path to the file where to write the data.
	 * @param string $contents The data to write.
	 * @param int    $mode     Optional. The file permissions as octal number, usually 0644.
	 * @return bool False on failure.
	 */
	public function put_contents($file, $contents, $mode = \false)
	{
	}
	/**
	 * Get the current working directory.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @return string|bool The current working directory on success, or false on failure.
	 */
	public function cwd()
	{
	}
	/**
	 * Change current directory.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $dir The new current directory.
	 * @return bool|string
	 */
	public function chdir($dir)
	{
	}
	/**
	 * Change the file group.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file      Path to the file.
	 * @param mixed  $group     A group name or number.
	 * @param bool   $recursive Optional. If set True changes file group recursively. Defaults to False.
	 * @return bool|string
	 */
	public function chgrp($file, $group, $recursive = \false)
	{
	}
	/**
	 * Change filesystem permissions.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file      Path to the file.
	 * @param int    $mode      Optional. The permissions as octal number, usually 0644 for files, 0755 for dirs.
	 * @param bool   $recursive Optional. If set True changes file group recursively. Defaults to False.
	 * @return bool|string
	 */
	public function chmod($file, $mode = \false, $recursive = \false)
	{
	}
	/**
	 * Get the file owner.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file Path to the file.
	 * @return string|bool Username of the user or false on error.
	 */
	public function owner($file)
	{
	}
	/**
	 * Get the file's group.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file Path to the file.
	 * @return string|bool The group or false on error.
	 */
	public function group($file)
	{
	}
	/**
	 * Copy a file.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $source      Path to the source file.
	 * @param string $destination Path to the destination file.
	 * @param bool   $overwrite   Optional. Whether to overwrite the destination file if it exists.
	 *                            Default false.
	 * @param int    $mode        Optional. The permissions as octal number, usually 0644 for files, 0755 for dirs.
	 *                            Default false.
	 * @return bool True if file copied successfully, False otherwise.
	 */
	public function copy($source, $destination, $overwrite = \false, $mode = \false)
	{
	}
	/**
	 * Move a file.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $source      Path to the source file.
	 * @param string $destination Path to the destination file.
	 * @param bool   $overwrite   Optional. Whether to overwrite the destination file if it exists.
	 *                            Default false.
	 * @return bool True if file copied successfully, False otherwise.
	 */
	public function move($source, $destination, $overwrite = \false)
	{
	}
	/**
	 * Delete a file or directory.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file      Path to the file.
	 * @param bool   $recursive Optional. If set True changes file group recursively. Defaults to False.
	 *                          Default false.
	 * @param bool   $type      Type of resource. 'f' for file, 'd' for directory.
	 *                          Default false.
	 * @return bool True if the file or directory was deleted, false on failure.
	 */
	public function delete($file, $recursive = \false, $type = \false)
	{
	}
	/**
	 * Check if a file or directory exists.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file Path to file/directory.
	 * @return bool Whether $file exists or not.
	 */
	public function exists($file)
	{
	}
	/**
	 * Check if resource is a file.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file File path.
	 * @return bool Whether $file is a file.
	 */
	public function is_file($file)
	{
	}
	/**
	 * Check if resource is a directory.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $path Directory path.
	 * @return bool Whether $path is a directory.
	 */
	public function is_dir($path)
	{
	}
	/**
	 * Check if a file is readable.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file Path to file.
	 * @return bool Whether $file is readable.
	 */
	public function is_readable($file)
	{
	}
	/**
	 * Check if a file or directory is writable.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file Path to file.
	 * @return bool Whether $file is writable.
	 */
	public function is_writable($file)
	{
	}
	/**
	 * Gets the file's last access time.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file Path to file.
	 * @return int|bool Unix timestamp representing last access time.
	 */
	public function atime($file)
	{
	}
	/**
	 * Gets the file modification time.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file Path to file.
	 * @return int|bool Unix timestamp representing modification time.
	 */
	public function mtime($file)
	{
	}
	/**
	 * Gets the file size (in bytes).
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file Path to file.
	 * @return int|bool Size of the file in bytes.
	 */
	public function size($file)
	{
	}
	/**
	 * Set the access and modification times of a file.
	 *
	 * Note: If $file doesn't exist, it will be created.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file  Path to file.
	 * @param int    $time  Optional. Modified time to set for file.
	 *                      Default 0.
	 * @param int    $atime Optional. Access time to set for file.
	 *                      Default 0.
	 * @return bool Whether operation was successful or not.
	 */
	public function touch($file, $time = 0, $atime = 0)
	{
	}
	/**
	 * Create a directory.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $path  Path for new directory.
	 * @param mixed  $chmod Optional. The permissions as octal number, (or False to skip chmod)
	 *                      Default false.
	 * @param mixed  $chown Optional. A user name or number (or False to skip chown)
	 *                      Default false.
	 * @param mixed  $chgrp Optional. A group name or number (or False to skip chgrp).
	 *                      Default false.
	 * @return bool False if directory cannot be created, true otherwise.
	 */
	public function mkdir($path, $chmod = \false, $chown = \false, $chgrp = \false)
	{
	}
	/**
	 * Delete a directory.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $path      Path to directory.
	 * @param bool   $recursive Optional. Whether to recursively remove files/directories.
	 *                          Default false.
	 * @return bool Whether directory is deleted successfully or not.
	 */
	public function rmdir($path, $recursive = \false)
	{
	}
	/**
	 * Get details for files in a directory or a specific file.
	 *
	 * @access public
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $path           Path to directory or file.
	 * @param bool   $include_hidden Optional. Whether to include details of hidden ("." prefixed) files.
	 *                               Default true.
	 * @param bool   $recursive      Optional. Whether to recursively include file details in nested directories.
	 *                               Default false.
	 * @return array|bool {
	 *     Array of files. False if unable to list directory contents.
	 *
	 *     @type string $name        Name of the file/directory.
	 *     @type string $perms       *nix representation of permissions.
	 *     @type int    $permsn      Octal representation of permissions.
	 *     @type string $owner       Owner name or ID.
	 *     @type int    $size        Size of file in bytes.
	 *     @type int    $lastmodunix Last modified unix timestamp.
	 *     @type mixed  $lastmod     Last modified month (3 letter) and day (without leading 0).
	 *     @type int    $time        Last modified time.
	 *     @type string $type        Type of resource. 'f' for file, 'd' for directory.
	 *     @type mixed  $files       If a directory and $recursive is true, contains another array of files.
	 * }
	 */
	public function dirlist($path, $include_hidden = \true, $recursive = \false)
	{
	}
}