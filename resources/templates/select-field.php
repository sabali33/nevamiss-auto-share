<?php

/**
 *
 * @var array{choices:array, value:mixed, label: string, id:string} $attributes
 * @var string $input_attr
 */
?>

<p class="description description-wide">
    <label for="<?php echo esc_attr($attributes['id']); ?>">
        <?php echo esc_html($attributes['label']); ?>
    </label>

    <select <?php echo $input_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> >
        <?php foreach( $attributes['choices'] as $key => $choice): ?>
            <?php $selected = $key === $attributes['value'] || (is_array($attributes['value']) && in_array($key, $attributes['value'])) ?
                'selected': ''
            ?>
            <option
                value="<?php echo esc_attr($key); ?>"
                <?php echo esc_attr($selected); ?>
            >
                <?php  echo esc_html($choice) ?>
            </option>
        <?php endforeach; ?>
    </select>
</p>