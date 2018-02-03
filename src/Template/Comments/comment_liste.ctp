<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Comment[]|\Cake\Collection\CollectionInterface $comments
 */

$title = 'Commentaires';
$this->assign('title',$title);
?>
<div class="comments index large-9 medium-8 columns content">
    <h3 class="mx-5 text-left bg-secondary text-white p-3"><?= __('Liste des commentaires') ?></h3>
    <div>
        <p class="float-left mb-0 ml-5 py-2">Trier par: </p>
        <ul class="nav">
            <li class="nav-item"><span class="nav-link"><?= $this->Paginator->sort('user_id') ?></span></li>
            <li class="nav-item"><span class="nav-link active"><?= $this->Paginator->sort('created') ?></span></li>
        </ul>
    </div>
    <h4 class="mb-3"><?= $survey->question; ?></h4>
    <div>
    <?php if($comments->count()) { ?>
        <?php foreach ($comments as $comment): ?>
        <div class="mx-5 text-left" style="border-top:1px dashed silver">
            <p class="mb-1 font-weight-bold">
                <?= $comment->has('user') ? $this->Html->link(ucfirst($comment->user->nickname), ['controller' => 'Users', 'action' => 'view', $comment->user->id]) : '' ?> - 
                <?= h($this->Time->format($comment->created, 'dd.MM.YYYY HH:mm')) ?>
            </p>
            <p><?= $comment->contenu; ?></p>
        </div>
        <?php endforeach; ?>
    <?php } else { ?>
        <div>Aucun commentaire pour ce sondage.</div>
    <?php } ?>
    </div>

    <div class="paginator">
        <ul class="pagination" style="display:inline-flex">
            <?= $this->Paginator->first('<< ' . __('first')) ?> 
            <?= $this->Paginator->prev('< ' . __('previous')) ?>&nbsp;
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>
</div>
