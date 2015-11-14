<?php
namespace MTG;
/*DON'T BE A DICK PUBLIC LICENSE

Everyone is permitted to copy and distribute verbatim or modified copies of this license document, and changing it is allowed as long as the name is changed.

    DON'T BE A DICK PUBLIC LICENSE TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

    Do whatever you like with the original work, just don't be a dick.

    Being a dick includes - but is not limited to - the following instances:

    1a. Outright copyright infringement - Don't just copy this and change the name.
    1b. Selling the unmodified original with no work done what-so-ever, that's REALLY being a dick.
    1c. Modifying the original work to contain hidden harmful content. That would make you a PROPER dick.

    If you become rich through modifications, related works/services, or supporting the original work, share the love. Only a dick would make loads off this work and not buy the original works creator(s) a pint.

    Code is provided with no warranty. Using somebody else's code and bitching when it goes wrong makes you a DONKEY dick. Fix the problem yourself. A non-dick would submit the fix back.
*/
if(!defined('MTG_ENABLE'))
	exit;
class items {
	static $inst = null;
	static public function getInstance() {
		if(self::$inst == null)
			self::$inst = new items();
		return self::$inst;
	}
	public function name($id, $link = true) {
		global $db, $mtg;
		if(!ctype_digit($id))
			return '<span class="red">Invalid item</span>';
		$db->query('SELECT `name` FROM `items` WHERE `id` = ?');
		$db->execute([$id]);
		if(!$db->num_rows())
			return '<span class="red">Invalid item</span>';
		$item = $mtg->format($db->fetch_single());
		return $link == true ? '<a href="items.php?action=info&amp;ID='.$id.'">'.$item.'</a>' : $item;
	}
	public function listAll($ddname = 'item', $selected = null, $notIn = [], $pure = '') {
		global $db, $mtg;
		$first = $selected == null ? 0 : 1;
		$ret = '<select name="'.$ddname.'"'.($pure ? ' class="'.$pure.'"' : '').'><option value="0"'.($selected == null ? ' selected="selected"' : '').'>--- Select ---</option>';
		$first = 1;
		$extra = '';
		if(count($notIn))
			$extra .= ' WHERE `id` NOT IN('.implode(',', $notIn).') ';
		$db->query('SELECT `id`, `name` FROM `items` '.$extra.' ORDER BY `name` ASC');
		$db->execute();
		$rows = $db->fetch_row();
		foreach($rows as $row) {
			$ret .= "\n".'<option value="'.$row['id'].'"';
			if($selected == $row['id'] || !$first || isset($_POST[$ddname]) && $_POST[$ddname] == $row['id']) {
				$ret .= ' selected="selected"';
				$first = 1;
			}
			$ret .= '>'.$mtg->format($row['name']).' ['.$mtg->format($row['id']).']</option>';
		}
		$ret .= "\n".'</select>';
		return $ret;
	}
}
$items = items::getInstance();