<?php

namespace Doe
{

	/**
	 * A log using json format
	 */
	class Logger
	{

		static private $logfiles = [];

		/**
		 * Write to default log stream
		 * 
		 * @param string $type Type of log entry. This an be any type of string
		 * @param mixed $args Any JSON serializable arguments
		 */
		public static function write(string $type, ...$args) : void
		{
			self::getStream('default')->write($type, ...$args);
		}

		/**
		 * Write to log stream
		 * 
		 * @param string $stream Stream to write to
		 * @param string $type Type of log entry. This an be any type of string
		 * @param mixed $args Any JSON serializable arguments
		 */
		public static function writeTo(string $stream, string $type, ...$args) : void
		{
			self::getStream($stream)->write($type, ...$args);
		}

		/**
		 * Get a reader for a log
		 * 
		 * @param string $stream Stream to read from
		 * @param float $startAt Timestamp to start from
		 * @param float $endAt Timestamp to end at (optional)
		 */
		public static function reader(string $stream, float $startAt, ?float $endAt = null) : Logger\Reader
		{
			return new Logger\Reader(self::getStream($stream), $startAt, $endAt);
		}

		/**
		 * Get stream
		 * 
		 * @param string $id Stream identifier
		 */
		protected function getStream(string $id) : Logger\Stream
		{
			if (!isset(self::$logfiles[$id])) {
				throw new Exception("The Doe\\Logger stream '" . $id . "' is not initialized", 1);
			}
			return self::$logfiles[$id];
		}

		/**
		 * Add default stream
		 * 
		 * @param string $path Path to where the files should be stored
		 */
		public static function addDefaultStream(string $path) : Logger\Stream
		{
			return self::addStream('default', $path);
		}

		/**
		 * Add a stream
		 * 
		 * @param string $id Stream identifier
		 * @param string $path Path to where the files should be stored
		 */
		public static function addStream(string $id, string $path) : Logger\Stream
		{
			return self::$logfiles[$id] = new Logger\Stream($id, $path);
		}

	}

}
