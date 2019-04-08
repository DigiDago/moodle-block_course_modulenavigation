<?php
// This file is part of The Course Module Navigation Block
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines the block strings.
 * @package    block_course_modulenavigation
 * @copyright  2019 Pimenko <contact@pimenko.com> <pimenko.com>
 * @author     Sylvain Revenu | Nick Papoutsis | Bas Brands | DigiDago
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['config_blocktitle']                   = 'Titre du block';
$string['config_blocktitle_default']           = 'Sommaire';
$string['config_blocktitle_help']              = 'Laisser ce champ vide pour utiliser le nom par défaut comme titre du block. Si vous ajoutez un tittre ici, il sera utilisée à la place de celui par défaut.';
$string['config_onesection']                   = 'Afficher uniquement dans le block la section active';
$string['config_onesection_label']             = 'Si cette option est activé, une seule section (celle qui est active) sera affichée. Si cette option est désactivée, le block sera affiché comme un menu accordéon avec toutes les sections';
$string['course_modulenavigation:addinstance'] = 'Ajouter un nouveau block de sommaire du cours (course contents block)';
$string['notusingsections']                    = 'Ce format de cours n&rsquo;utilise pas de section. ';
$string['pluginname']                          = 'Sommaire du cours';
$string['toggleclickontitle']                  = 'En cliquant sur le titre';
$string['toggleclickontitle_desc']             = "Afficher le menu ou aller à la page";
$string['toggleclickontitle_menu']             = 'Afficher le menu';
$string['toggleclickontitle_page']             = 'Aller à la page';
$string['toggleshowlabels']                    = 'Afficher les étiquettes';
$string['toggleshowlabels_desc']               = 'Choisir d\'afficher ou non les étiquettes';
$string['togglecollapse']                      = 'Déplier les tabs';
$string['togglecollapse_desc']                 = 'Choisir de déplier tous les tabs par défaut';
$string['toggletitles']                        = 'Afficher les titres';
$string['toggletitles_desc']                   = 'Choisir d\'afficher que les titres ou les titres et le contenu';
$string['privacy:null_reason']                 = 'Le bloc sommaire du cours affiche seulement des données enregistrées à d\'autres endroits';