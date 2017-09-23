<?php
namespace Commando\Type;

class Option extends Enum
{
    const Default = 0x01;
    
    const Argument = 0x01;
    const Short = 0x02;
    const Long = 0x04;
}
