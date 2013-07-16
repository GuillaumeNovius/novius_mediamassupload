<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */


Nos\I18n::current_dictionary(array('novius_mediamassupload::common', 'nos::common'));

$uniqid = uniqid('id_');

$fields = array(
    'media[]' => array(
        'form' => array(
            'type' => 'file',
            'multiple' => true,
        ),
        'validation' => array(
            'required',
        ),
        'label' => __('File from your hard drive:'),
    ),
    'unzip' => array(
        'form' => array(
            'type' => 'select',
            'options' => array(
                'unzip' => __('Unzip before adding'),
                'leave' => __('Leave the zipped file'),
            ),
        ),
        'label' => __('Zip files:'),
    ),
    'media_folder_id' => array(
        'renderer' =>  'Nos\Media\Renderer_Folder',
        'form' => array(
            'type'  => 'hidden',
        ),
        'label' => __('Select a folder where to put your media files:'),
    ),
    'save' => array(
        'label' => '',
        'form' => array(
            'type' => 'submit',
            'tag' => 'button',
            // Note to translator: This is a submit button
            'value' => __('Save'),
            'class' => 'ui-priority-primary',
            'data-icon' => 'check',
        ),
    ),
);
$fieldset = \Fieldset::build_from_config($fields);
$form_attributes = $fieldset->get_config('form_attributes', array());
$form_attributes['enctype'] = 'multipart/form-data';
$fieldset->set_config('form_attributes', $form_attributes);
$fieldset->js_validation();

echo $fieldset->open('admin/novius_mediamassupload/upload/save');
$fieldset->form()->set_config('field_template', '{field}');
echo $fieldset->build_hidden_fields();

?>
<div class="page line">
    <div class="col c1"></div>
    <div class="col c10">
        <div class="line" style="margin-bottom:1em;">
            <table class="fieldset standalone">
                <tr class="title">
                    <th><?= $fieldset->field('media[]')->label ?></th>
                    <td><?= $fieldset->field('media[]')->build() ?></td>
                </tr>
                <tr>
                    <th></th>
                    <td style="font-style: italic;">
                        <p><?= strtr(__('Caution! The maximum file size should not exceed {{size}}.'), array('{{size}}' => ini_get('upload_max_filesize'))) ?></p>
                        <p><?= strtr(__('These extensions are not allowed: {{extensions}}.'), array('{{extensions}}' => implode(', ', \Config::get('novius-os.upload.disabled_extensions', array('php'))))) ?></p>
                    </td>
                </tr>
                <tr class="zip-file" style="display: none;">
                    <th><?= $fieldset->field('unzip')->label ?></th>
                    <td><?= $fieldset->field('unzip')->build() ?></td>
                </tr>
                <tr>
                    <th><?= $fieldset->field('media_folder_id')->label ?></th>
                    <td><?= $fieldset->field('media_folder_id')->build() ?></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="col c1"></div>
</div>
<?= $fieldset->close() ?>

<?php
    $json = array(
        'tabParams' => $tab_params,
        'saveField' => (string) \View::forge('form/layout_save', array('save_field' => $fieldset->field('save')), false),
        'maxFileSize' => \Num::bytes(ini_get('upload_max_filesize')),
        'texts' => array(
            'exceedMaxSize' => strtr(
                __('The total file size exceeds the limit upload {{size}}.'),
                array('{{size}}' => ini_get('upload_max_filesize'))
            ),
        ),
    );
?>
<script type="text/javascript">
require(
    [
        'static/apps/novius_mediamassupload/js/jquery.novius-os.media-mass-upload'
    ],
    function($) {
        $(function() {
            $('#<?= $fieldset->form()->get_attribute('id') ?>')
                .nosMediaMassUpload(<?= \Format::forge($json)->to_json() ?>);
        });
    });
</script>