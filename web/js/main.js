$(document).ready( function () {

  $('.login.user').click(function (e) {
    e.preventDefault();
    $(this).parents('form').find('input').each(function () {
      if($(this).attr('required') && $(this).val() == '') {
        $(this).siblings('.invalid-feedback').show();
        return false;
      } else {
        $(this).siblings('.invalid-feedback').hide();
      }
    });
    $('#form-user').submit();
  });

  $('.login.operator').click(function (e) {
    e.preventDefault();
    $(this).parents('form').find('input').each(function () {
      if($(this).attr('required') && $(this).val() == '') {
        $(this).siblings('.invalid-feedback').show();
        return false;
      } else {
        $(this).siblings('.invalid-feedback').hide();
      }
    });
    $('#form-operator').submit();
  });

  $('.user-profile').click(function (e) {
    document.location.href = '/operator/instances/' + $(this).attr('id');
  });

  $('.user-edit').click(function (e) {
    e.preventDefault();
    var id = $(this).attr('data-id');
    var flow = $(this).attr('data-flow');
    var date = $(this).attr('data-date');
    var type = $(this).attr('data-type');
    if (date.length > 0) {
      document.location.href = '/' + type +'/questions/' + id + '/' + flow + '/' +  date;
    } else {
      document.location.href = '/' + type +'/questions/' + id + '/' + flow;
    }
    return true;
  });

  $('.user-view').click(function (e) {
    e.preventDefault();
    var id = $(this).attr('data-id');
    var flow = $(this).attr('data-flow');
    var date = $(this).attr('data-date');
    var type = $(this).attr('data-type');
    if (date.length > 0) {
      document.location.href = '/' + type +'/questions-view/' + id + '/' + flow + '/' +  date;
    } else {
      document.location.href = '/' + type +'/questions-view/' + id + '/' + flow;
    }
    return true;
  });

  $('#search-field').keyup(function () {
    $('.user-list tr').show();
    var text = $('#search-field').val();
    $('.user-list tbody tr').each(function () {
      var haystack = $(this).text().toUpperCase();
      var needle = text.toUpperCase();
      if(haystack.indexOf(needle) < 0) {
        $(this).hide();
      } else {
        $(this).show();
      }
    });
  });
});

$(document).on('click','#new-patient', function () {
  if($('#new-patient-code').hasClass('shown')) {
    $('#new-patient-code').removeClass('shown');
    $('#new-patient-code').slideUp();
  } else {
    $('#new-patient-code').addClass('shown');
    $('#new-patient-code').slideDown();
  }
})
$(document).on('click','#new-patient-send',function () {
  var code = $('#new-patient-field').val();
  var name = $('#new-patient-name-field').val();
  if(code == '') {
    alert('Missing required field');
    return false;
  }
  $.ajax({
    url: '/operator/new-patient',
    type: 'POST',
    data: {'code': code,'name': name},
    success: function (data) {
      location.reload();
    },
    error: function (data) {
      //alert("This code is already taken");
    }
  });
});



$(document).ready(function () {

  if($('table.user-list').length > 0) {
    $('table.user-list').DataTable({
      "scrollY": "800px",
      "scrollX": true,
      "scrollCollapse": true,
      "columnDefs": [
        { "orderable": false, "targets": [2,3,4,5] }
      ],
      "pageLength": 10
    }
    );
  }

  $('.submit-button').on('click',function () {
    window.location.href = '/operator/user-list'
  })

  $('.list-group-item-custom').on('click',function(e) {
    e.stopPropagation();
    $(this).find('input').click();
  });

  $('.list-group-item-custom input').click(function (e) {
    e.stopPropagation();
  })

  $('.question input').on('change',function () {
    var its = $(this);
    $(this).parents('.question').find('.checked').removeClass('checked');
    its.parents('.list-group-item-custom').addClass('checked');
    its.parents('.question').find('input').each(function () {
      if($(this).attr('data-option-id') != its.attr('data-option-id')) {
        $(this).prop('checked',false);
        if($(this).attr('type') == 'numeric' || $(this).attr('type') == 'text' || $(this).attr('type') == 'date') {
          $(this).val('')
        } else {
          $(this).parents('.list-group-item-custom').find('i').addClass('far');
          $(this).parents('.list-group-item-custom').find('i').removeClass('fas');
        }
      }
    });
    its.parents('.list-group-item-custom').find('i').addClass('fas');
    its.parents('.list-group-item-custom').find('i').removeClass('far');
    var role = $(this).attr('data-role');
    var question_id = $(this).attr('data-question-id');
    var user_id = $(this).attr('data-user-id');
    var option_id = $(this).attr('data-option-id');
    var date = $(this).attr('data-date');
    var value = $(this).val();
    //$('#spinner-'+question_id).show();
    var data = {'question_id': question_id,
                'user_id': user_id,
                'option_id': option_id,
                'date': date,
                'value': value}
    $.ajax({
      url: '/'+role+'/save-answer',
      type: 'POST',
      data: data,
      success: function (data) {

      },
      error: function (data) {
        alert('An error occurred.');
      }
    }).done(function () {

      //$('#spinner-'+question_id).fadeOut();
    })

  })
});
