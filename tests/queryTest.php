<?php
namespace App;
use PHPUnit\Framework\TestCase;

final class QueryTest extends TestCase
{

 
    // public function testStringsBetween() : void {
    //     $this->assertEquals(
    //         'pollo',
    //         $this->home->string_between_two_string('(pollo)','(',')')
    //     );
    // }

    public function testIfThisWorks() : void 
    {
        $stack = [];
        $this->assertSame(0, count($stack));

        array_push($stack, 'foo');
        $this->assertSame('foo', $stack[count($stack)-1]);
        $this->assertSame(1, count($stack));

        $this->assertSame('foo', array_pop($stack));
        $this->assertSame(0, count($stack));
    }

    public function testManejador() : void
    {   
        $a = 'pollo';
        $output = $this->request('POST',['Order', 'upload',$a] );

    }
}