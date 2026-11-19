/**
 * DRF Treffen-Anmeldung - Modern Multi-Step Wizard JavaScript
 */
jQuery(document).ready(function ($) {
  var form = $('#drfRegistrationForm');
  if (!form.length) return;

  function formatCurrency(val) {
    return val.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
  }

  // 1. Wizard Step Navigation Logic
  function goToStep(stepNumber) {
    stepNumber = parseInt(stepNumber, 10);
    if (isNaN(stepNumber) || stepNumber < 1 || stepNumber > 4) return;

    // Validate if advancing from step 1
    if (stepNumber > 1) {
      var fname = $('#reg_firstname').val().trim();
      var lname = $('#reg_lastname').val().trim();
      var email = $('#reg_email').val().trim();

      if (!fname || !lname || !email) {
        alert('Bitte gib mindestens Vorname, Nachname und eine gültige E-Mail-Adresse an, bevor du fortfährst.');
        if (!fname) $('#reg_firstname').focus();
        else if (!lname) $('#reg_lastname').focus();
        else $('#reg_email').focus();
        return;
      }

      // Basic email syntax check
      var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        alert('Bitte gib eine gültige E-Mail-Adresse ein.');
        $('#reg_email').focus();
        return;
      }
    }

    // Update Step Contents
    $('.drf-form-step-content').removeClass('active');
    $('.drf-form-step-content[data-step-content="' + stepNumber + '"]').addClass('active');

    // Update Progress Bar
    $('.drf-step-item').each(function () {
      var s = parseInt($(this).data('step'), 10);
      $(this).removeClass('active completed');
      if (s === stepNumber) {
        $(this).addClass('active');
      } else if (s < stepNumber) {
        $(this).addClass('completed');
      }
    });

    // Update connecting lines
    $('.drf-steps-progress .step-line').each(function (index) {
      if (index + 1 < stepNumber) {
        $(this).addClass('active');
      } else {
        $(this).removeClass('active');
      }
    });

    // If entering Step 4, compile review summary
    if (stepNumber === 4) {
      compileReviewData();
    }

    // Scroll smoothly to top of form wrapper
    var wrapper = $('#drfAnmeldungWrapper');
    if (wrapper.length) {
      $('html, body').animate({
        scrollTop: wrapper.offset().top - 80
      }, 300);
    }
  }

  // Next / Prev button triggers
  $(document).on('click', '.drf-btn-next', function (e) {
    e.preventDefault();
    var target = $(this).data('target');
    goToStep(target);
  });

  $(document).on('click', '.drf-btn-prev', function (e) {
    e.preventDefault();
    var target = $(this).data('target');
    goToStep(target);
  });

  // Direct click on completed steps in progress bar
  $(document).on('click', '.drf-step-item.completed', function () {
    var target = $(this).data('step');
    goToStep(target);
  });

  // 2. Room Card Selection
  $(document).on('click', '.drf-room-card', function () {
    $('.drf-room-card').removeClass('selected');
    $(this).addClass('selected');
    var radio = $(this).find('input[type="radio"]');
    radio.prop('checked', true).trigger('change');
  });

  // 3. Duration Pill Selection
  $(document).on('click', '.drf-duration-pill', function () {
    $('.drf-duration-pill').removeClass('selected');
    $(this).addClass('selected');
    var radio = $(this).find('input[type="radio"]');
    radio.prop('checked', true).trigger('change');
  });

  // 4. Diet Chip Selection
  $(document).on('click', '.drf-diet-chip', function () {
    $('.drf-diet-chip').removeClass('active');
    $(this).addClass('active');
    var radio = $(this).find('input[type="radio"]');
    radio.prop('checked', true).trigger('change');
  });

  // 5. Room Partner Toggle (for DZ)
  $('input[name="room_category"]').on('change', function () {
    if ($(this).val() === 'dz') {
      $('#drfRoomPartnerGroup').slideDown(200);
    } else {
      $('#drfRoomPartnerGroup').slideUp(200);
    }
    updateCalculation();
  });

  // 6. Participation Type Change
  $('input[name="participation_type"]').on('change', function () {
    updateCalculation();
  });

  // 7. Shuttle Service Toggle
  $('#reg_shuttle_needed').on('change', function () {
    if ($(this).is(':checked')) {
      $('#drfShuttleDetails').slideDown(200);
    } else {
      $('#drfShuttleDetails').slideUp(200);
    }
  });

  // 8. Social Donation Pill Selection
  $('input[name="social_donation_select"]').on('change', function () {
    $('.drf-don-pill').removeClass('active');
    $(this).closest('.drf-don-pill').addClass('active');

    if ($(this).val() === 'custom') {
      $('#drfCustomDonationGroup').slideDown(200);
    } else {
      $('#drfCustomDonationGroup').slideUp(200);
      $('#reg_social_donation').val($(this).val());
    }
    updateCalculation();
  });

  $('#reg_social_donation').on('input', function () {
    updateCalculation();
  });

  // 9. Recalculate Price Breakdown in Realtime
  function updateCalculation() {
    var roomInput = $('input[name="room_category"]:checked');
    var partInput = $('input[name="participation_type"]:checked');

    var price = parseFloat(roomInput.data('price')) || 0;
    var factor = parseFloat(partInput.data('factor')) || 1;

    var baseAmount = price * factor;

    // Social Donation
    var donationVal = $('input[name="social_donation_select"]:checked').val();
    var donation = 0;
    if (donationVal === 'custom') {
      donation = parseFloat($('#reg_social_donation').val()) || 0;
    } else {
      donation = parseFloat(donationVal) || 0;
      $('#reg_social_donation').val(donation);
    }

    var total = baseAmount + donation;

    $('#drfSummaryBase').text(formatCurrency(baseAmount));
    $('#drfSummaryDonation').text(formatCurrency(donation));
    $('#drfSummaryTotal').text(formatCurrency(total));
  }

  // 10. Compile Step 4 Review Summary
  function compileReviewData() {
    var fname = $('#reg_firstname').val().trim() || '—';
    var lname = $('#reg_lastname').val().trim() || '';
    var fullName = (fname + ' ' + lname).trim();
    var nick = $('#reg_nickname').val().trim() || 'ohne Nickname';
    var email = $('#reg_email').val().trim() || '—';
    var phone = $('#reg_phone').val().trim();
    var contact = email + (phone ? ' • Tel: ' + phone : '');

    var roomCard = $('input[name="room_category"]:checked').closest('.drf-room-card');
    var roomTitle = roomCard.find('.room-title').text() || 'Einzelzimmer (EZ)';
    var roomPartner = $('#reg_room_partner').val().trim();
    if ($('input[name="room_category"]:checked').val() === 'dz' && roomPartner) {
      roomTitle += ' (Partner: ' + roomPartner + ')';
    }

    var dietVal = $('input[name="diet"]:checked').val();
    var dietLabel = 'Normalkost / Allesesser';
    if (dietVal === 'vegetarian') dietLabel = 'Vegetarisch';
    if (dietVal === 'vegan') dietLabel = 'Vegan';

    var allergies = $('#reg_diet_allergies').val().trim();
    if (allergies) {
      dietLabel += ' (Allergien: ' + allergies + ')';
    }

    var shuttleNeeded = $('#reg_shuttle_needed').is(':checked');
    var shuttleText = 'Nicht benötigt (eigene Anreise)';
    if (shuttleNeeded) {
      var station = $('#reg_shuttle_station option:selected').text();
      var arrival = $('#reg_shuttle_arrival').val().trim();
      shuttleText = 'Ja: ' + station + (arrival ? ' um ' + arrival : '');
    }

    $('#revName').text(fullName);
    $('#revNick').text(nick);
    $('#revContact').text(contact);
    $('#revRoom').text(roomTitle);
    $('#revDiet').text(dietLabel);
    $('#revShuttle').text(shuttleText);

    updateCalculation();
  }

  // Initial calculation
  updateCalculation();

  // 11. Final Form Submission via AJAX
  form.on('submit', function (e) {
    e.preventDefault();

    var feedback = $('#drfFormFeedback');
    var submitBtn = $('#drfSubmitBtn');

    feedback.hide().removeClass('success error');
    submitBtn.prop('disabled', true).addClass('loading').find('span:first').text('Buchung wird übermittelt...');

    var formData = form.serialize();

    $.ajax({
      url: window.drfAnmeldungData ? window.drfAnmeldungData.ajaxUrl : '/wp-admin/admin-ajax.php',
      type: 'POST',
      data: formData,
      dataType: 'json',
      success: function (res) {
        submitBtn.prop('disabled', false).removeClass('loading').find('span:first').text('Verbindlich anmelden & Platz sichern →');
        if (res.success) {
          feedback.addClass('success').html('<strong>🎉 Herzlichen Glückwunsch!</strong> ' + res.data.message).slideDown(300);
          form[0].reset();
          updateCalculation();
          // Reset wizard to step 1
          setTimeout(function() {
            goToStep(1);
          }, 3500);
          $('html, body').animate({
            scrollTop: feedback.offset().top - 120
          }, 400);
        } else {
          feedback.addClass('error').html('<strong>Hinweis:</strong> ' + (res.data.message || 'Fehler beim Absenden der Anmeldung.')).slideDown(300);
          $('html, body').animate({
            scrollTop: feedback.offset().top - 120
          }, 400);
        }
      },
      error: function () {
        submitBtn.prop('disabled', false).removeClass('loading').find('span:first').text('Verbindlich anmelden & Platz sichern →');
        feedback.addClass('error').html('<strong>Fehler:</strong> Der Server antwortet nicht. Bitte prüfe deine Verbindung oder kontaktiere das Tagungsbüro.').slideDown(300);
        $('html, body').animate({
          scrollTop: feedback.offset().top - 120
        }, 400);
      }
    });
  });
});
