<?php


if (!function_exists('toSelect2Data')) {
    function toSelect2Data($args, $key_id, $key_text, $key_text_adds = null, $spec = null): array
    {
        if (!is_array($args)) {
            try {
                $args->toArray();
            } catch (\Exception $exception) {
                return [];
            }
        }

        if (count($args) == 0) {
            return [];
        }

        $result = array_filter(array_map(function ($item) use ($key_id, $key_text, $key_text_adds, $spec) {
            if ($key_text == 'running_number_text') {
                return [
                    'id' => $item[$key_id],
                    'text' => $item[($item['job_stage'] == 'incoming' ? 'running_number_inc' : 'running_number')] . ($key_text_adds ? $spec . $item[$key_text_adds] : null)
                ];
            } else {
                if (!empty($item[$key_text])) {
                    return [
                        'id' => $item[$key_id],
                        'text' => $item[$key_text] . ($key_text_adds ? $spec . $item[$key_text_adds] : null)
                    ];
                }
            }

            return null;
        }, $args));

        return array_values($result);
    }
}
