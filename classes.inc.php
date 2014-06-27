<?php

class ImageContent {
	const ITEMS_PER_PAGE = 20;
	var $page = 0;
	var $tags = '';
	var $minus = '';
	var $sorting = 'date';
	var $db;

	function __construct($db) {
		$this->db = $db;
	}	
	
	private function GetAllTags() {
		$output = '';
		$result = mysql_query("select tag_name from tags order by tag_name ", $this->db);
		$tags = array();
		while ($row = mysql_fetch_array($result)) {
			$tags[] = $row[0];
		}
		$output = implode(', ',$tags);
		return $output;
	}
	
	private function TagsToArray($tags_str) {
		$output = array();
		if ($tags_str!='')
		{
			$items = explode(",",$tags_str);
			foreach ($items as $item) {
				$output[] = trim($item);
			}
			asort($output);
			return $output;
		} else {
			return false;		
		}
	}
	
	private function GetQuery() {
		$current_tags = $this->TagsToArray($this->tags);
		$current_minus = $this->TagsToArray($this->minus);
		
		$tags = '%';
		if ($current_tags!==false) {
			$tags .= implode(',%',$current_tags).'%';
		} 			
		
		$query = sprintf(" FROM photos p LEFT JOIN users u ON u.user_id = p.user_id WHERE tags LIKE '%s'", mysql_real_escape_string($tags));
		if ($current_minus!==false) {
			foreach ($current_minus as $minus_tag) {
				$query .= sprintf(" AND tags NOT LIKE '%s' ", '%'.mysql_real_escape_string($minus_tag).'%');
			}
		}	
		
		switch ($this->sorting) {
			case 'date': $query .= ' ORDER BY created_at DESC'; break;
			case 'likes': $query .= ' ORDER BY likes DESC'; break;
			default: $query .= ' ORDER BY created_at DESC'; break;
		}
		
		return $query;
	}
	
	function Filters() {
		$output = '<h2>Фильтр</h2>';
		$output .= '<form action = "/" method = "GET">';
		$output .= '<h3>Доступные теги:</h3><p>'.$this->GetAllTags().'</p>';
		$output .= '<p><label>Выбранные теги через запятую:</label><input type="text" name="tags" size="80" value="'.$this->tags.'"/></p>';
		$output .= '<p><label>Исключающие теги через запятую:</label><input type="text" name="minus" size="80" value="'.$this->minus.'"/></p>';
		$output .= '<p><label>Сортировать по:</label><select name="sorting">';
		if ($this->sorting=='date') {
			$output .= '<option value = "date" selected>дате</option>';
			$output .= '<option value = "likes">количеству лайков</option>';
		} else {
			$output .= '<option value = "date">дате</option>';
			$output .= '<option value = "likes" selected>количеству лайков</option>';
		}
		$output .= '</select></p>';
		$output .= '<input type = "submit" value = "Применить">';
		$output .= '</form>';
		return $output;
	}	
	
	function GetImages() {
		$output = '';
		$query = 'SELECT p.src, p.created_at, u.user_name, p.tags, p.likes'.$this->GetQuery();
		$current_item = $this->page * self::ITEMS_PER_PAGE;
		$query .= ' LIMIT '.$current_item.', '.self::ITEMS_PER_PAGE;
		$page_number = $this->page + 1;
		$output .='<h1>А вот что мы нашли (страница '.$page_number.'):</h1>';
		$photos = mysql_query($query);
		$results_count = 0;
		while ($photo = mysql_fetch_assoc($photos)) {
			$output .= '<div><img class="lazy" data-original="'.$photo['src'].'" width="600">';
			$output .= '<p><strong>Автор:</strong> '.$photo['user_name'].'</strong><br />';
			$output .= '<strong>Теги:</strong> '.$photo['tags'].'</strong><br />';
			$output .= '<strong>Количество лайков:</strong> '.$photo['likes'].'</strong><br />';
			$output .= '<strong>Дата:</strong> '.$photo['created_at'].'</strong><br />';
			$output .= '</div><hr />';
			$results_count++;
		}
		if ($results_count == 0) {
			$output .= '<p>Ничего не найдено :(</p>';
		}
		return $output;
	}	
	
	function Pager() {
		$output = '<div>';
		$query = 'SELECT count(p.photo_id) as cnt'.$this->GetQuery();
		$result = mysql_query($query);
		$total_count = mysql_fetch_assoc($result);
		$pages_count = ceil($total_count['cnt'] / self::ITEMS_PER_PAGE);
		for ($page = 1; $page <= $pages_count; $page++) {
			if (($page==1)||($page==$pages_count)||(abs($this->page+1-$page)<2))
			{
				if ($page==$this->page+1) {
					$output .= '<strong>'.$page.'</strong>&nbsp;&nbsp;';
				} else {
					$page_index = $page-1;
					$output .= '<a href="?page='.$page_index.'&tags='.$this->tags.'&minus='.$this->minus.'&sorting='.$this->sorting.'">'.$page.'</a>&nbsp;&nbsp;';
				}
			}
			
			if ((abs($this->page+1-$page)==2)) {
				$output .= '...&nbsp;&nbsp;';
			}
		}
		$output .= '</div>';
		return $output;
	}	
	



}
?>