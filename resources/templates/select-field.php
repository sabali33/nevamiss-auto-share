<?php

/**
 *
 * @var array{choices:array, value:mixed, label: string, id:string} $attributes
 * @var string $input_attr
 */
?>

<p class="description description-wide">
    <label for="<?php esc_attr_e($attributes['id']); ?>">
        <?php esc_html_e($attributes['label']); ?>
    </label>

    <select <?php echo $input_attr; ?> >
        <?php foreach( $attributes['choices'] as $key => $choice): ?>
            <?php $selected = $key === $attributes['value'] || (is_array($attributes['value']) && in_array($key, $attributes['value'])) ?
                'selected': ''
            ?>
            <option
                value="<?php esc_attr_e($key); ?>"
                <?php esc_attr_e($selected); ?>
            >
                <?php  esc_html_e($choice) ?>
            </option>
        <?php endforeach; ?>
    </select>
</p>