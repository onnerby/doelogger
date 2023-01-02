<?php

namespace Doe\Logger
{

	/**
	 * A log stream
	 * 
	 * This class is more or less just a identifier and the path to where the files are stored
	 */
	class Stream
	{

		public $id;
		public $path;

		public function __construct(string $id, string $path)
		{
			$this->id = $id;
			$this->path = $path;
		}

		/**
		 * Write to log.
		 * This will log the log file while writing for a "safe" log
		 * 
		 * @param string $type Type of log entry
		 * @param mixed $args Variable number of args that should be JSON serializable
		 */
		public function write(string $type, ...$args)
		{
			// Open file for writing
			$out = [
				'when' => microtime(true),
				'type' => $type,
				'content' => $args,
			];
			$logFile = $this->path . '_' . date('Y-m-d') . '.json';
			$fp = fopen($logFile, 'a');
			if ($fp === false) {
				throw new Exception("Unable to open logfile " . $logFile, 1);
			}
			if (flock($fp, LOCK_EX)) {
				fwrite($fp, json_encode($out) . ",\n");
			} else {
				// Unable to block
			}
			fclose($fp);
		}



	}

}
