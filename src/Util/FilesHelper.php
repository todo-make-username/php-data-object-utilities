<?php declare(strict_types=1);

namespace TodoMakeUsername\DataObjectUtilities\Util;

class FilesHelper
{
	/**
	 * Get the raw file data from $_FILES or an empty array if file data empty.
	 *
	 * @param string $file_name The file data that you want.
	 * @return array
	 */
	public static function getRawFileData(string $file_name): array
	{
		return (self::isFileDataEmpty($_FILES[$file_name] ?? []) ? [] : $_FILES[$file_name]);
	}

	/**
	 * Check if the upload is empty.
	 *
	 * @param array $file_data The upload data to check. Either multi or single upload.
	 * @return boolean
	 */
	public static function isFileDataEmpty(array $file_data): bool
	{
		if (empty($file_data))
		{
			return true;
		}

		// Formatted multi-file upload
		if (isset($file_data[0]))
		{
			return ($file_data[0]['error'] === UPLOAD_ERR_NO_FILE);
		}

		// Single file upload
		if (!is_array($file_data['error']))
		{
			return ($file_data['error'] === UPLOAD_ERR_NO_FILE);
		}

		// Multi-file upload
		return (count($file_data['error']) === 1 && $file_data['error'][0] === UPLOAD_ERR_NO_FILE);
	}

	/**
	 * Converts the multi-uploads in $_FILES array to a readable format.
	 *
	 * @param array $files The field array in the file array to format.
	 * @return array
	 */
	public static function formatMultiFileData(array $files): array
	{
		// Return if formatted already
		if (is_array($files) && (empty($files) || isset($files[0])))
		{
			return $files;
		}

		// already a single file
		if (!is_array($files['error']))
		{
			return (self::isFileDataEmpty($files)) ? [] : $files;
		}

		$file_count       = count($files['error']);
		$file_keys        = array_keys($files);
		$formatted_array  = [];
		$new_file_counter = 0;

		for ($i = 0; $i < $file_count; $i++)
		{
			if ($files['error'][$i] === UPLOAD_ERR_NO_FILE)
			{
				continue;
			}

			$formatted_array[$new_file_counter] = [];

			foreach ($file_keys as $key)
			{
				$formatted_array[$new_file_counter][$key] = $files[$key][$i];
			}

			$new_file_counter++;
		}

		return $formatted_array;
	}
}