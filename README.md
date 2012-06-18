WBL_Modules
===========

The WBL-Autoloader helps you to create OXID-Modules in a "zend-frameworkish" Style without 
the typical redundancy of creating a nice module directory structure and cloning this 
structure into your module config and dirnames in the class name.

The Autoloader does only work, if you merge the given functions.php 
with your own functions.php. 

The Autoloader searches a module in the given base dir (WBL_Modules_Autoloader::setBaseDir(string)), 
converting the namespace-backslash or the underscore to the directory 
separator. As usual, you start your module class names with your namespace, which
you should provide with WBL_Modules_Autoloader::setAutoloaderNamespaces(array()). 

The core overload WBL_Modules_UtilsObject is necessary for OXID >= 4.6.0.

The Autoloader is 100% tested with unittests till 4.6.0.



This program is free software: you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.