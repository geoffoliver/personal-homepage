<?php
use App\Lib\oEmbed;
$oEmbed = oEmbed::getInstance();

$this->assign('title', $post->name);
$this->append('css', $this->Html->css('posts/view.css'));
$this->append('script', $this->Html->script('posts/view.js'));

$content = $this->element(
    'posts/content',
    ['content' => $post->content]/*,
    ['cache' => [
        'key' => "post_{$post->id}_{$post->modified->format('U')}",
        'config' => 'posts'
    ]]*/
);

$hasEmbed = strpos($content, 'pf-oembed') !== false;

?>
<section class="section" id="viewPost">
    <article>
        <div class="columns">
            <div class="column is-three-quarters">
                <div class="post-name">
                    <h1 class="title is-3"><?= $post->name; ?></h1>
                    <h2 class="subtitle is-6 has-text-grey">
                        <?= __('Posted'); ?>&nbsp;<time><?= $post->created->format('F j, Y \a\t g:i a'); ?></time>
                    </h2>
                </div>
                <div class="post-body">
                    <div class="box">
                        <?php if ($post->content): ?>
                            <div class="post-content content">
                                <?= $content; ?>
                                <?php
                                    //echo $this->cache(function() use ($hasEmbed, $post, $oEmbed) {
                                        if (!$hasEmbed && $post->source) {
                                            $embed = $oEmbed->getEmbedInfo($post->source);
                                            if ($embed && $embed->code) {
                                                $hasEmbed = true;
                                                echo $oEmbed->wrapEmbed($embed->code);
                                            }
                                        }
                                    //}, ['key' => "embed_{$post->id}_{$post->modified->format('U')}"]);
                                ?>
                            </div>
                        <?php endif; ?>
                        <?php if (
                            $post->medias && (
                                $post->import_source !== 'twitter' || !$hasEmbed
                            )
                        ): ?>
                            <div class="post-media">
                                <?php
                                    foreach ($post->medias as $media) {
                                        echo $this->element('media', ['media' => $media]);
                                    }
                                ?>
                            </div>
                        <?php endif; ?>
                        <hr />
                        <?= $this->element('item-footer', ['item' => $post, 'comments' => false]); ?>
                    </div>
                </div>
            </div>
            <div class="column is-one-quarter">
                <?= $this->element('comments', ['post' => $post]); ?>
            </div>
        </div>
    </article>
</section>
