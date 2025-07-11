

$(document).on('change', '.checkboks', function(e){
    let id = $(this).val();
    if($(this).is(':checked')){
        idData.push(id);
        checkedContent();
    }else {
        removeA(idData, id);
        uncheckedContent();
    }
    DeleteAll();
});

$(document).on('click', '.checkall', function(e){
    var ischecked= $(this).is(':checked');
    if(ischecked){
        $('input[type=checkbox]').prop('checked', 'checked');
        checkAll();
    }else {
        $('input[type=checkbox]').prop('checked', '');
        uncheckAll();
    }
    DeleteAll();
});

function removeA(arr) {
    var what, a = arguments, L = a.length, ax;
    while (L > 1 && arr.length) {
        what = a[--L];
        while ((ax= arr.indexOf(what)) !== -1) {
            arr.splice(ax, 1);
        }
    }
    return arr;
}

function checkedContent()
{
    $.each(idData, function(index, value){
        $('#select'+value).prop("checked", true);
    });

    var numberOfChecked = $('input:checkbox[name=check_data]:checked').length;
    var numberChecked = $('input:checkbox[name=check_data]').length;


    if(numberOfChecked > 0 && numberOfChecked < numberChecked)
    {
        $('#checkAll').html('<span id="unchecked" style="cursor: pointer;"><i class="fa fa-minus-square text-primary"></i></span>');
    }
    if(numberOfChecked === 0){
        $('#checkAll').html('');
    }

    if(numberChecked > 0 && numberOfChecked <= 0){
        $('#checkAll').html(`<input type="checkbox" class="checkall" >`);
    }
    if(numberOfChecked === numberChecked)
    {
        if(numberOfChecked === 0 && numberChecked === 0)
        {
            $('#checkAll').html(``);
        }else {
            $('#checkAll').html(`<input type="checkbox" class="checkall"  checked>`);
        }

    }
}

function uncheckedContent()
{
    var numberOfChecked = $('input:checkbox[name=check_data]:checked').length;

    var numberChecked = $('input:checkbox[name=check_data]').length;
    if(numberOfChecked === 0){
        $('#checkAll').html(`<input type="checkbox" class="checkall"/>`);
    }
    if(numberOfChecked > 0){
        $('#checkAll').html(`<span id="unchecked" style="cursor: pointer;"><i class="fa fa-minus-square text-primary"></i></span>`);
    }
}

function checkAll()
{
    $("input:checkbox[name=check_data]:checked").each(function(){
        idData.push($(this).val());
    });
}

function uncheckAll(){
    $("input:checkbox[name=check_data]").each(function(){
        var val = $(this).val();
        idData.splice(idData.indexOf(val), 1);
    });
}

function DeleteAll()
{
    if(idData.length > 0)
    {
        $('.delete-all').show();
        $(".update-status").prop('disabled', false);
        $('#lengthcek').html(' ('+idData.length+')');
    } else {
        $('.delete-all').hide();
        $(".update-status").prop('disabled', true);

    }
}
