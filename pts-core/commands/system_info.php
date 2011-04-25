<?php

/*
	Phoronix Test Suite
	URLs: http://www.phoronix.com, http://www.phoronix-test-suite.com/
	Copyright (C) 2008 - 2011, Phoronix Media
	Copyright (C) 2008 - 2011, Michael Larabel

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

class system_info implements pts_option_interface
{
	const doc_section = 'System';
	const doc_description = 'Display the installed system hardware and software information as detected by the Phoronix Test Suite Phodevi Library.';

	public static function run($r)
	{
		pts_client::$display->generic_heading('System Information');
		echo 'Hardware:' . PHP_EOL . phodevi::system_hardware(true) . PHP_EOL . PHP_EOL;
		echo 'Software:' . PHP_EOL . phodevi::system_software(true) . PHP_EOL . PHP_EOL;

		// echo phodevi::read_property('motherboard', 'serial-number');
	}
}

?>
