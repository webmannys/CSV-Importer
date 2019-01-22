# Drupal 8 CSV Importer
Drupal 8 Custom Module to import data into Article nodes from a CSV. Also can attach images to these nodes if there is a filename in the CSV.

### Installing
Copy or clone the project into the modules/custom folder.
Enable the module (CSV Import) in Extend /admin/modules page.

### Usage
Create a shortcut (/admin/config/user-interface/shortcut) for the CSV Import path (/admin/csvimport).
Go to the CSV Import page using the shortcut or go to it directly (/admin/csvimport).
Click on the link for the blank csv to download the csv file.
Fill in the csv file with the Article node title in the title column, body in the body column, and the filename in the file column if there is a file (not required).
Upload the files to the public://upload folder. 
On the CSV Import page, select the CSV file by clicking 'Choose File'.
Click on 'Upload CSV' button to run the import.
If there is a problem, you will get a link to see the Error Logs, or you can go directly to (/admin/reports/dblog).
