(function() {
  var $ = function(qs) {
    return document.querySelector(qs);
  };

  var $$ = function (qs) {
    return document.querySelectorAll(qs);
  };

  // if there's no table, there's nothing else to do here
  if (!$('table')) {
    return;
  }

  var form = $('#unapprovedComments'); // the actual form
  var checkAll = $('#check-all'); // the checkbox at the top of the list
  var approveChecked = $('#approve-checked'); // the link to approve all the checked comments
  var deleteApproved = $('#delete-checked'); // the link to delete all the checked comments
  var approveSingle = $$('.approve-comment'); // the link to approve a single comment
  var deleteSingle = $$('.delete-comment'); // the link to delete a single comment

  // toggle the checkboxes when a user toggles the 'check all' checkbox
  checkAll.addEventListener('change', function() {
    var checkboxes = $$('tbody input[type="checkbox"]');
    for (let checkbox of checkboxes) {
      checkbox.checked = checkAll.checked;
    }
  });

  // handle a click on the 'approve all checked' button
  approveChecked.addEventListener('click', function() {
    var checked = $$('tbody input:checked');

    // if there's nothing to approve, throw an error up and call it a day
    if (checked.length === 0) {
      alert("You must select at least one comment to approve.");
      return false;
    }

    // make sure we really want to do this
    if (confirm('Are you sure you want to approve all of the selected comments?')) {
      var action = document.createElement('input');
      action.type = 'hidden';
      action.name = 'group_action';
      action.value = 'approve';
      form.appendChild(action);
      form.submit();
    }

    return false;
  });

  // handle a click on the 'delete all checked' button
  deleteApproved.addEventListener('click', function() {
    var checked = $$('tbody input:checked');

    // if there's nothing to delete, throw an error up and call it a day
    if (checked.length === 0) {
      alert("You must select at least one comment to delete.");
      return false;;
    }

    // make sure we really want to do this
    if (confirm('Are you sure you want to delete all of the selected comments?')) {
      var action = document.createElement('input');
      action.type = 'hidden';
      action.name = 'group_action';
      action.value = 'delete';
      form.appendChild(action);
      form.submit();
    }

    return false;
  });

  var doApproveSingle = function(event) {
    if (confirm('Are you sure you want to approve this comment?')) {
      var action = document.createElement('input');
      var checkbox = event.target.closest('tr').querySelector('input');

      checkbox.checked = true;
      action.type = 'hidden';
      action.name = 'group_action';
      action.value = 'approve';
      form.appendChild(action);
      form.submit();
    }

    return false;
  };

  var doDeleteSingle = function() {
    if (confirm('Are you sure you want to delete this comment?')) {
      var action = document.createElement('input');
      var checkbox = event.target.closest('tr').querySelector('input');

      checkbox.checked = true;
      action.type = 'hidden';
      action.name = 'group_action';
      action.value = 'delete';
      form.appendChild(action);
      form.submit();
    }

    return false;
  };

  for (var i = 0; i < approveSingle.length; i++) {
    approveSingle.item(i).addEventListener('click', doApproveSingle)
  }

  for (var i = 0; i < deleteSingle.length; i++) {
    deleteSingle.item(i).addEventListener('click', doDeleteSingle);
  }

})();
