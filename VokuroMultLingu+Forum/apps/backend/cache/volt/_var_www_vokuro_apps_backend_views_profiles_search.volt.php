<?php echo $this->getContent(); ?>

<ul class="pager">
    <li class="previous pull-left">
        <?php echo $this->tag->linkTo(array('profiles/index', '&larr; Go Back')); ?>
    </li>
    <li class="pull-right">
        <?php echo $this->tag->linkTo(array('profiles/create', 'Create profiles', 'class' => 'btn btn-primary')); ?>
    </li>
</ul>

<?php $v15455126242599158161iterated = false; ?><?php $v15455126242599158161iterator = $page->items; $v15455126242599158161incr = 0; $v15455126242599158161loop = new stdClass(); $v15455126242599158161loop->length = count($v15455126242599158161iterator); $v15455126242599158161loop->index = 1; $v15455126242599158161loop->index0 = 1; $v15455126242599158161loop->revindex = $v15455126242599158161loop->length; $v15455126242599158161loop->revindex0 = $v15455126242599158161loop->length - 1; ?><?php foreach ($v15455126242599158161iterator as $profile) { ?><?php $v15455126242599158161loop->first = ($v15455126242599158161incr == 0); $v15455126242599158161loop->index = $v15455126242599158161incr + 1; $v15455126242599158161loop->index0 = $v15455126242599158161incr; $v15455126242599158161loop->revindex = $v15455126242599158161loop->length - $v15455126242599158161incr; $v15455126242599158161loop->revindex0 = $v15455126242599158161loop->length - ($v15455126242599158161incr + 1); $v15455126242599158161loop->last = ($v15455126242599158161incr == ($v15455126242599158161loop->length - 1)); ?><?php $v15455126242599158161iterated = true; ?>
<?php if ($v15455126242599158161loop->first) { ?>
<table class="table table-bordered table-striped" align="center">
    <thead>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Active?</th>
        </tr>
    </thead>
<?php } ?>
    <tbody>
        <tr>
            <td><?php echo $profile->id; ?></td>
            <td><?php echo $profile->name; ?></td>
            <td><?php echo ($profile->active == 'Y' ? 'Yes' : 'No'); ?></td>
            <td width="12%"><?php echo $this->tag->linkTo(array('profiles/edit/' . $profile->id, '<i class="icon-pencil"></i> Edit', 'class' => 'btn')); ?></td>
            <td width="12%"><?php echo $this->tag->linkTo(array('profiles/delete/' . $profile->id, '<i class="icon-remove"></i> Delete', 'class' => 'btn')); ?></td>
        </tr>
    </tbody>
<?php if ($v15455126242599158161loop->last) { ?>
    <tbody>
        <tr>
            <td colspan="10" align="right">
                <div class="btn-group">
                    <?php echo $this->tag->linkTo(array('profiles/search', '<i class="icon-fast-backward"></i> First', 'class' => 'btn')); ?>
                    <?php echo $this->tag->linkTo(array('profiles/search?page=' . $page->before, '<i class="icon-step-backward"></i> Previous', 'class' => 'btn ')); ?>
                    <?php echo $this->tag->linkTo(array('profiles/search?page=' . $page->next, '<i class="icon-step-forward"></i> Next', 'class' => 'btn')); ?>
                    <?php echo $this->tag->linkTo(array('profiles/search?page=' . $page->last, '<i class="icon-fast-forward"></i> Last', 'class' => 'btn')); ?>
                    <span class="help-inline"><?php echo $page->current; ?>/<?php echo $page->total_pages; ?></span>
                </div>
            </td>
        </tr>
    <tbody>
</table>
<?php } ?>
<?php $v15455126242599158161incr++; } if (!$v15455126242599158161iterated) { ?>
    No profiles are recorded
<?php } ?>
