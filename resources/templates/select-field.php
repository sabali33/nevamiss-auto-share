<?php

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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

    <?php echo wp_kses_post(wp_kses_attr('select', $input_attr, 'post', ['http', 'https'])); ?>
        <?php foreach( $attributes['choices'] as $key => $choice): ?>
            <?php $selected = $key === $attributes['value'] || (is_array($attributes['value']) && in_array($key, $attributes['value'])) ?
                'selected': '';
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