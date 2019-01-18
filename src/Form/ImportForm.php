<?php
/**
 * @file
 * Contains \Drupal\csvimport\Form\ImportForm.
 */
namespace Drupal\csvimport\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;

class ImportForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'csvimport_form';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['description'] = array(
      '#markup' => '<h4>Use this form to upload a CSV file of Data to create Article nodes</h4>
                    <p>Download this <a href="/sites/default/files/importcsv/blank.csv" target="_blank">blank csv</a> to enter the data.</p>',
    );

    $form['import_csv'] = array(
      '#type' => 'managed_file',
      '#title' => t('Upload file here'),
      '#upload_location' => 'public://importcsv/',
      '#default_value' => '',
      "#upload_validators"  => array("file_validate_extensions" => array("csv")),
      '#states' => array(
        'visible' => array(
          ':input[name="File_type"]' => array('value' => t('Upload Your File')),
        ),
      ),
    );

    $form['actions']['#type'] = 'actions';


    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Upload CSV'),
      '#button_type' => 'primary',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {


    /* Fetch the array of the file stored temporarily in database */
    $csv_file = $form_state->getValue('import_csv');

    /* Load the object of the file by it's fid */
    $file = File::load( $csv_file[0] );

    /* Set the status flag permanent of the file object */
    $file->setPermanent();

    /* Save the file in database */
    $file->save();

    // You can use any sort of function to process your data. The goal is to get each 'row' of data into an array
    // If you need to work on how data is extracted, process it here.
    $data = $this->csvtoarray($file->getFileUri(), ',');
    foreach($data as $row) {
      $operations[] = ['\Drupal\csvimport\addImportContent::addImportContentItem', [$row]];
    }

    $batch = array(
      'title' => t('Importing Data...'),
      'operations' => $operations,
      'init_message' => t('Import is starting.'),
      'finished' => '\Drupal\csvimport\addImportContent::addImportContentItemCallback',
    );
    batch_set($batch);
  }

  public function csvtoarray($filename='', $delimiter){

    if(!file_exists($filename) || !is_readable($filename)) return FALSE;
    $header = NULL;
    $data = array();

    if (($handle = fopen($filename, 'r')) !== FALSE ) {
      while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
      {
        if(!$header){
          $header = $row;
        }else{
          $data[] = array_combine($header, $row);
        }
      }
      fclose($handle);
    }

    return $data;
  }

}