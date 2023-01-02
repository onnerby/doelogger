<?php

namespace Doe\Logger
{

	/**
	 * Reader of log file
	 */
	class Reader implements \Iterator
	{

		public $stream;
		public $startAt;
		public $endAt;

		private $currentAt;
		private $currentLogDate;
		private $currentLog;
		private $currentLogPos = 0;
		private $currentContent;

		/**
		 * Constructor
		 * 
		 * @param Stream $stream
		 * @param float $startAt Timestamp to start at
		 * @param float $endAt (optional)
		 */
		public function __construct(Stream $stream, float $startAt, ?float $endAt = null)
		{
			$this->stream = $stream;
			$this->startAt = $startAt;
			if ($endAt == null) {
				$endAt = microtime(true);
			}
			$this->endAt = $endAt;
		}

		/**
		 * Read log from current time.
		 */
		private function readLog() : bool
		{
			$logFile = $this->stream->path . '_' . date('Y-m-d', $this->currentAt) . '.json';
			if (!file_exists($logFile)) {
				return false;
			}
			if ($log = file($logFile)) {
				$this->currentLog = array_reverse($log);
				$this->currentLogPos = 0;
				$this->currentLogDate = date('Y-m-d', $this->currentAt);
				return true;
			}
			return false;
		}

		/**
		 * Read next log. Meaning that we locate the next logfile to read that is in our range of start and end
		 */
		private function readNextLog()
		{
			while(!$this->readLog()) {
				if ($this->currentAt < $this->startAt) {
					return false;
				}
				$this->currentAt = strtotime('-1 day', $this->currentAt);
			}
			return true;
		}

		/**
		 * Start all over again
		 */
		public function rewind()
		{
			$this->currentAt = $this->endAt;
			$this->readNextLog();
		}

		public function current()
		{
			return $this->currentContent;
		}

		/**
		 * Get key
		 * 
		 * The key is the date + current position
		 */
		public function key()
		{
			return $this->currentLogDate . '_' . $this->currentLogPos;
		}

		public function next()
		{
			++$this->currentLogPos;
		}

		public function valid() {
			if (isset($this->currentLog[$this->currentLogPos])) {
				$this->currentContent = json_decode(substr($this->currentLog[$this->currentLogPos], 0, -2), true);
				$tmp = json_decode(substr($this->currentLog[$this->currentLogPos], 0, -2), true);
				if ($this->currentContent && $this->currentContent['when'] >= $this->startAt) {
					return true;
				}
			}
			// Continue to previous date
			$this->currentAt = strtotime('-1 day', $this->currentAt);
			return $this->readNextLog();
		}
		

	}

}
