<!DOCTYPE html>
<html lang="en">
    <?php foreach($percentage_bmri as $pbmri){
        echo $pbmri['date'];
        echo $pbmri['percentage'];
    } ?>
    <?php foreach($percentage_jci as $pjci){
        echo $pjci['date'];
        echo $pjci['percentage'];
    } ?>
</html>

