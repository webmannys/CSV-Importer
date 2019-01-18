<?php

namespace Drupal\csvimport;

use Drupal\node\Entity\Node;
use \Drupal\file\Entity\File;

class addImportContent
{
    public static function addImportContentItem($item, &$context)
    {
        $connection = \Drupal::database();
        $context['sandbox']['current_item'] = $item;
        $message = 'Creating ' . $item['title'];
        $results = array();

        self::csv_add_article($item);

        $context['message'] = $message;
        $context['results'][] = $item;
    }

    public static function addImportContentItemCallback($success, $results, $operations)
    {
        // The 'success' parameter means no fatal PHP errors were detected. All
        // other error management should be handled using 'results'.
        if ($success) {
            $message = \Drupal::translation()->formatPlural(
                count($results),
                'One item processed.', '@count items processed.'
            );
        } else {
            $message = t('Finished with an error.');
        }
        drupal_set_message($message);
    }

    // Create a new Article node
    private static function csv_add_article($item)
    {

        if (!file_destination('public://upload/' . $item['title'], FILE_EXISTS_ERROR))
        {
            // Create file object from a locally copied file.
            $uri = file_unmanaged_copy('public://upload/' . $item['title'] . '.jpg', 'public://csv_images/' . $item['title'] . '.jpg', FILE_EXISTS_REPLACE);

            $file = File::Create([
                'uri' => $uri,
            ]);
            $file->save();
        }

        $node_data['type'] = 'article';
        $node_data['title'] = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $item['title']);
        $node_data['body']['value'] = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $item['body']);


        if (!file_destination('public://upload/' . $item['title'], FILE_EXISTS_ERROR))
        {
            $node_data['field_image']['target_id'] = $file->id();
        }

        $node = Node::create($node_data);
        $node->setPublished(TRUE);
        $node->save();
    }

}
