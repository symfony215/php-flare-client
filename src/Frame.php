<?php

namespace Spatie\FlareClient;

use Spatie\Backtrace\Frame as SpatieFrame;

class Frame
{
    protected SpatieFrame $frame;

    public static function fromSpatieFrame(SpatieFrame $frame)
    {
        return new static($frame);
    }

    public function __construct(SpatieFrame $frame)
    {
        $this->frame = $frame;
    }

    public function toArray(): array
    {
        return [
            'file' => $this->frame->file,
            'line_number' => $this->frame->lineNumber,
            'arguments' => $this->frame->arguments,
            'method' => $this->frame->method,
            'class' => $this->frame->class,
            'code_snippet' => $this->frame->getSnippet(30),
            'application_frame' => $this->frame->applicationFrame,
        ];
    }
}
