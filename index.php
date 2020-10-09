<?php

/**
 * @file plugins/generic/psychopen-aam/index.php
 *
 * This file is part of AAMPlugin (https://github.com/leibniz-psychology/psychopen-aam).
 *
 * OpenID Authentication Plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OpenID Authentication Plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OpenID Authentication Plugin.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @file OrcidProfilePlugin.inc.php
 * @copyright 2020 Leibniz Institute for Psychology Information (https://leibniz-psychology.org/)
 * @ingroup plugins_generic_psychopen-aam
 * @brief Wrapper for AAM Plugin.
 *
 */
require_once('AAMPlugin.inc.php');

return new AAMPlugin();


