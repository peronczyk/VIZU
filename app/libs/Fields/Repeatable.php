<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Field: Repeatable
 *
 * =================================================================================
 */

namespace Fields;

class Repeatable {
	const FIELD_TYPE = 'repeatable';

	private $_template;


	/** ----------------------------------------------------------------------------
	 * Constructor
	 */

	public function __construct(\Template $template) {
		$this->_template = $template;
	}


	/** ----------------------------------------------------------------------------
	 * Convert above flat array into 2 dimensional.
	 * Keys `something__1` become `something[1]`;
	 */

	public static function getSubfieldsValuesFromFieldContent(string $saved_data) {
		$field_content_data = json_decode($saved_data, true);
		$groups_number = $field_content_data['groups-number'] ?? null;

		if (!$groups_number) {
			return [0, []];
		}

		unset($field_content_data['groups-number']);

		$values = [];
		foreach ($field_content_data as $key => $val) {
			$key_chunks = explode('__', $key);
			if (!isset($values[$key_chunks[0]])) {
				$values[$key_chunks[0]] = [];
			}
			array_push($values[$key_chunks[0]], $val);
		}

		return [
			$groups_number,
			$values
		];
	}


	/** ----------------------------------------------------------------------------
	 * Assign values taken from the database
	 */

	public function assignValues($fields_data) {
		$started = false;
		$started_key = null;
		$started_id = null;
		$child_field_ids = [];

		foreach ($this->_template->template_fields as $key => $field) {
			// Closing tag
			if ($field['type'] == '/' . self::FIELD_TYPE) {
				$groups_number = 0;

				if (isset($fields_data[$started_id])) {
					$content = json_decode($fields_data[$started_id]['content'], true);
					if (is_array($content)) {
						$this->_template->template_fields[$started_key]['value'] = $content;
					}
				}

				unset($this->_template->template_fields[$key]);
				$started = false;
				$child_field_ids = [];
			}

			// Child fields that are placed between opening and closing tags
			elseif ($started) {
				$this->_template->template_fields[$started_key]['children'][] = $field;
				unset($this->_template->template_fields[$key]);
				array_push($child_field_ids, $field['props']['id']);
			}

			// Opening tag
			elseif ($field['type'] == self::FIELD_TYPE) {
				$started = true;
				$started_key = $key;
				$started_id = $field['props']['id'];
			}
		}

		$this->_template->template_fields = array_values($this->_template->template_fields);
	}


	/** ----------------------------------------------------------------------------
	 * Repeatable fields behaves differently than regular template fields, so this
	 * method parses them before main parsing operation does.
	 * @todo code below was created at fast so it should be rewiten.
	 *   Maybe parsing hook?
	 */

	public function preParse($fields_data) {
		$template_file_content = $this->_template->getTemplateFileContent();

		$this->_template->iterateTemplateFieldsType(self::FIELD_TYPE, function($key, $field) use ($template_file_content, $fields_data) {
			$field_id = $field['props']['id'];
			$field_data = $fields_data[$field_id] ?? null;
			$group_parsed_code = "<!-- " . self::FIELD_TYPE . " : {$field_id} -->";
			$groups_number = 0;

			/**
			 * Get saved array of values of this repeatable field
			 */
			if (isset($field_data) && isset($field_data['content'])) {
				list($groups_number, $field_content_data) = self::getSubfieldsValuesFromFieldContent($field_data['content'] ?? null);
			}

			/**
			 * Get inner html code of repeatable block
			 */
			$pattern = '/' . $field['tag'] . '(.*){{ \/' . self::FIELD_TYPE . ' }}/us';
			preg_match($pattern, $template_file_content, $match);
			$repeatable_inner_code = $match[1] ?? '';

			/**
			 * Prepare repeated content with fields replaced with values
			 */
			$inner_fields = \Template::getFieldsFromString($repeatable_inner_code);

			// Iterate group number times
			for ($i = 0; $i < $groups_number; $i++) {
				$group_replace = [];

				// Iterate each of he group subfields
				foreach ($inner_fields as $subfield) {
					$subfield_id = $subfield['props']['id'];
					$group_replace[$subfield['tag']] = $field_content_data[$subfield_id][$i] ?? "<!-- {$subfield_id} -->";
				}

				$group_parsed_code .= strtr($repeatable_inner_code, $group_replace);
			}

			/**
			 * Final replacement of whole repeatable field
			 */
			$replace_from = $field['tag'] . $repeatable_inner_code . '{{ /' . self::FIELD_TYPE . ' }}';
			$replace_to = (!empty($group_parsed_code))
				? $group_parsed_code
				: "<!-- " . self::FIELD_TYPE . " : {$field_id} -->";

			$this->_template->replaceTemplateFileContent(str_replace($replace_from, $replace_to, $template_file_content));
		});

		$this->_template->removeFieldType(self::FIELD_TYPE);
	}
}