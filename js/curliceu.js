var guid;
    var dots = 1;
    var materialCost = 0;
    var chainCost = 0;
    var shippingCost = 0;

    var fonts = null;
    var popularNames=["Hannah","Emily","Sarah","Madison","Brianna","Kaylee","Kaitlyn","Hailey","Alexis","Elizabeth","Taylor","Lauren","Ashley","Katherine","Jessica","Anna","Samantha","Makayla","Kayla","Madeline","Jasmine","Alyssa","Abigail","Olivia","Brittany","Nicole","Destiny","Mackenzie","Emma","Jennifer","Rachel","Sydney","Megan","Grace","Alexandra","Morgan","Savannah","Victoria","Sophia","Natalie","Amanda","Stephanie","Chloe","Allison","Rebecca","Jacqueline","Julia","Cheyenne","Amber","Erica","Isabella","Kylie","Christina","Brooke","Bailey","Maria","Diana","Danielle","Kelsey","Jordan","Andrea","Vanessa","Melissa","Kimberly","Sierra","Maya","Michelle","Caroline","Arianna","Zoe","Leslie","Isabel","Gabrielle","Faith","Lindsey","Erin","Kiara","Jenna","Casey","Paige","Mary","Alicia","Cameron","Alexandria","Molly","Melanie","Katie","Courtney","Trinity","Jada","Claire","Audrey","Adriana","Mia","Margaret","Riley","Jocelyn","Gabriela","Sabrina","Miranda"];

    var slideNames = ["Hannah","Kaylee","Madison","Elizabeth"];

    function SVG(tag) {
       return document.createElementNS('http://www.w3.org/2000/svg', tag);
    }

    function getFontByName(name) {
      var retVal = null;
      $.each(fonts,function(index,value) {
        if (value.name == name) {
          retVal = value;
          return false;
        }
      });
      return retVal;
    }

    function getMaterialCost(element) {
      var retVal = parseInt(0);
      $.each($(element).parents(".material"), function(index,value) {
        if ($(value).attr("rel-price")) {
          retVal += parseInt($(value).attr("rel-price"));
        }
      });
      return retVal;
    }
    function getMaterialName(element) {
      var retVal = "";
      $.each($(element).parents(".material"),function(index,val) {
        retVal += $(this).attr("rel-materialType");
        retVal += " ";
      });
      return retVal;
    }

    var currentNameIndex = 0;
    function getNextPopularName () {
      return popularNames[currentNameIndex++ % popularNames.length];
    }

    function copyFromBilling() {
      $("#shipName").val($("#billName").val());
      $("#shipAddress1").val($("#billAddress1").val());
      $("#shipAddress2").val($("#billAddress2").val());
      $("#shipCity").val($("#billCity").val());
      $("#shipState").val($("#billState").val());
      $("#shipZip").val($("#billZip").val());
    }


    function getChainCost() {
      var retVal = 0;
      if ($("input[type='radio'][name='chainLength']:checked").val() == "24") {
        retVal = 5;
      } 
      return retVal;
    }

    function enableCheckoutIfReady() {
      if ($(".selectedScript").length && $(".chosenMaterial").length) {
        $("#checkout").prop('disabled', false);
        $("#orderSummary").show();
      } else {
        $("#checkout").prop('disabled', true);
      }
    }

    function getSubtotal() {
      return getMaterialCost($(".chosenMaterial").first()) + getChainCost();
    }

    function getTotal() {
      return getSubtotal() + getShippingCost();
    }

    function getShippingCost() {
      console.log($("#shippingMethod option:selected"));
      return parseInt($("#shippingMethod option:selected").attr("rel-price"));
    }

    function updateOrderTotal() {
      var total =  getTotal();
      $("#orderTotal").html("$" + total);
    }

    function updateOrderSubTotal() {
      $("#orderSubtotal").html("$" + getSubtotal());
    }

    function refreshExamples(doScroll,name) {


      var newText = $("#name").val();
      var newSize = 36;
      $(".preview").addClass("armedStyle");
      $(".sample").addClass("armedStyle");
      if (!newText || newText.length ==0) {
        newText = (!name || name.length == 0) ? getNextPopularName() : name;
//        newText = getNextPopularName();
        /*$.each($('.expanded'),function(index,val) {
              $(val).click();      
          });*/

      } else {
        $("#scripts").css("color","#303030").css("opacity",".8");
        //$(".styleHead").show();
        
      }
      if (newText.length > 8) {
        newSize = 32-(4*(newText.length - 8));
      }
     $.each($(".preview"),function(index,val) {
      $(this).fadeOut(750,function() {
        $(this).text(newText)
      }).fadeIn(750);
     // $(val).css("font-size",newSize + "px");
     });
     $(".dynamic").show();
     if (doScroll) {
                $('html,body').animate({
              scrollTop: $("#styleHeader").offset().top -25
            }, 1000);
     }
      //$(document).scrollTop( $("#styleHeader").offset().top ); 
    }

      $(document).foundation({
        orbit: {
          animation: 'slide',
          timer_speed: 5000,
          pause_on_hover: true,
          animation_speed: 1000,
          navigation_arrows: true,
          bullets: true,
          timer:true,
          resume_on_mouseout:true
        }
      });  

      $(document).ready(function() {
        $.getJSON( "fonts.json", function( data ) {
          fonts = data;
          initApp();
        });
      });
      
      function initApp() {
 
        $.each(fonts,function(index,val) {
          var id = val.name.replace(" ", "-");
          var fontSpacing = "";

          var scriptDiv = "<div  id='" 
                          + id 
                          + "' class='preview large-3 medium-4 small-6 columns";
          if (val.class) {
            scriptDiv     += " " 
                          + val.class
                          
          }
          scriptDiv       += "' "
                          + "style='font-family:\""
                          + val.name
                          + "\";";
          if (val.style) {
            scriptDiv     += val.style
          }
                          + "' ";
          scriptDiv       += "'alt='"
                          + val.name
                          + "'></div>";

          $("#scripts").append(scriptDiv);
        });

        $("#photos").on("after-slide-change.fndtn.orbit", function(event, orbit) {
          if ($("#name").val().length ==0) {
            var activeBullet = $(".orbit-bullets").find(".active").first().attr("data-orbit-slide");
            refreshExamples(false,slideNames[activeBullet]);
          }
          console.info("after slide change");
          console.info("slide " + orbit.slide_number + " of " + orbit.total_slides);
        });

      refreshExamples();

        $(".preview").click(function() {
             if ($(this).hasClass("selectedScript")) {
               $(".selectedScript").removeClass("selectedScript");
               return;
             }
            $(".selectedScript").removeClass("selectedScript");

            $(this).addClass("selectedScript");
            $("#fontName").html($(this).attr("id"));
            //$(this).text($(this).attr("id"));

            //$(".material").show();


            $('html,body').animate({
              scrollTop: $("#materialHeader").offset().top - 25
            }, 1000);
            
            var chosenFont = getFontByName($(this).attr('id').replace("-"," "));
            var imagePreviewUrl =  "https://www.curlic.eu/namesvg/?text=" +
                                $(this).html() +
                                "&font=" + 
                                chosenFont.ttfName +
                                "&stroke=" +
                                ((chosenFont.stroke && chosenFont.stroke.length > 0) ? chosenFont.stroke : "") +
                                "&spacing=" +
                                ((chosenFont.spacing && chosenFont.spacing.length > 0) ? chosenFont.spacing : "")
                                "&size=" +
                                ((chosenFont.size && chosenFont.size.length > 0) ? chosenFont.size : "")
                                "&t=" +
                                ((chosenFont.top && chosenFont.top.length > 0) ? chosenFont.top : "")
                                "&l=" +
                               ((chosenFont.left && chosenFont.left.length > 0) ? chosenFont.left : "")
            $("#orderName").html("<img src='" + imagePreviewUrl + "'>");
            enableCheckoutIfReady();
        });

        $(".sample").click(function() {
          if ($(this).parent().hasClass("exemplar")) {
            return;
          }
          $(".chosenMaterial").removeClass("chosenMaterial");
          $(this).addClass("chosenMaterial");
          $(".order").show();
          $('html,body').animate({
              scrollTop: $("#orderHeader").offset().top -25
            }, 1000);
          $("#orderMaterial").html(getMaterialName(this));
          materialCost = getMaterialCost(this);
          updateOrderSubTotal();
          updateOrderTotal();
          enableCheckoutIfReady();
        });

        $('#shippingMethod').on('change', function() {
          updateOrderTotal();
        });

        $(".menuToggle").click(function(event) {
          console.log(event);
          var isExpanded = !$(this).hasClass('expanded'); //it *was* expanded before the click
          var ourList = this;
          $.each($('.expanded'),function(index,val) {
            if (val != ourList) {
              $(val).click();
            }
          });
          $(this).toggleClass('expanded',isExpanded);
          $(this).find(".fa").toggleClass("fa-minus-square-o",isExpanded);
          $(this).find(".fa").toggleClass("fa-plus-square-o",!isExpanded);
          $(this).parent().find(".expandedList").toggle(isExpanded);
          $(this).parent().find(".expandedList").toggleClass("highlighted",isExpanded);
          $(this).parent().find(".exemplar").toggle(!isExpanded);

        });

        $("#name").on("input",function() {
          if ($("#name").val().length ==0) {
            //$(".dynamic").hide();
            refreshExamples();
          } 
        });

        $("input[name=chainLength]:radio").change(function () {
          
          updateOrderTotal();
          updateOrderSubTotal();
        });

      $(document).keypress(function(e) {
        if(e.which == 13) {
          event.stopPropagation();
          refreshExamples($("#name").val());
        }
      });

      Stripe.setPublishableKey('pk_VpNFyzG0XC2rL0pV1QlDvQ3rCUSea');

        var stripeResponseHandler = function(status, response) {
          var $form = $('#payment-form');

          if (response.error) {
            // Show the errors on the form
            $form.find('.payment-errors').text(response.error.message);
            $form.find('button').prop('disabled', false);
          } else {
            // token contains id, last4, and card type
            var token = response.id;
            // Insert the token into the form so it gets submitted to the server
            $form.append($('<input type="hidden" name="stripeToken" />').val(token));
            $form.append($('<input type="hidden" name="price" />').val(parseInt(getTotal()) * 100));
            $form.append($('<input type="hidden" name="material" />').val(getMaterialName($(".chosenMaterial").first())));
            $form.append($('<input type="hidden" name="text" />').val($("#name").val()));
            $form.append($('<input type="hidden" name="font" />').val($(".selectedScript").first().attr("id")));

            // and re-submit
            $form.get(0).submit();
          }
        };

      jQuery(function($) {
        $('#payment-form').submit(function(e) {
          var $form = $(this);

          // Disable the submit button to prevent repeated clicks
          $form.find('button').prop('disabled', true);

          Stripe.card.createToken($form, stripeResponseHandler);
          // Prevent the form from submitting with the default action

          return false;
        });
      });
  }

