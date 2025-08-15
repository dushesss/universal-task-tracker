<?php

declare(strict_types=1);

namespace UniversalTaskTracker\DTO;

/**
 * Task
 *
 * Simple DTO for a task with normalized fields and raw provider data.
 */
class Task
{
	/** @var string */
	public $id;
	/** @var string|null */
	public $title;
	/** @var string|null */
	public $description;
	/** @var string|null */
	public $assignee;
	/** @var string|null */
	public $status;
	/** @var array|null */
	public $raw;

	public function __construct(string $id, ?string $title = null, ?string $description = null, ?string $assignee = null, ?string $status = null, ?array $raw = null)
	{
		$this->id = $id;
		$this->title = $title;
		$this->description = $description;
		$this->assignee = $assignee;
		$this->status = $status;
		$this->raw = $raw;
	}
} 