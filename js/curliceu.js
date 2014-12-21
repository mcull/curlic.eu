var guid;
    var dots = 1;
    var materialCost = 0;
    var chainCost = 0;
    var shippingCost = 0;

    var fonts = [ 
                  {"name":"acryle_script_personal_useRg","ttfName":"Acryle Script Personal Use","style":"font-size:2em;"},
                  {"name":"Yellowtail"},
                  {"name":"Lily Script One"},
                  {"name":"Oleo Script Swash Caps","style":"letter-spacing:-2px:font-size:1.3em"},
                  {"name":"Norican"},
                  {"name":"Yesteryear"},
                  {"name":"Pacifico"},
                  {"name":"Special Elite","style":"letter-spacing:-8px;font-size:1.3em"},
                  {"name":"Slackey","style":"letter-spacing:-5px"},
                  {"name":"da_streetsregular","ttfName":"Da Streets","style":"font-size:1.3em;letter-spacing:-3px"},
                  {"name":"Dancing Script","ttfName":"Dancing Script Bold","style":"font-size:1.3em;font-weight:bold"},
                  {"name":"alfa_slab_oneregular","ttfName":"Alfa Slab One","style":"letter-spacing:-3px"},
                  {"name":"impregnable_personal_use_onRg","ttfName":"Impregnable Personal Use Only","style":"font-size:2em;"},
                  {"name":"japanese_brushregular","ttfName":"Japanese Brush","style":"letter-spacing:-5px"},
                  {"name":"oldnewspapertypesregular","ttfName":"OldNewspaperTypes","style":"letter-spacing:-4px"},
                  {"name":"mastoc_personal_use_onlyRg","ttfName":"Mastoc Personal Use Only","style":"font-size:1.3em"},
                  {"name":"olivierregular","ttfName":"olivier","style":"font-size:1.2em"},
                  {"name":"intrique_script_personal_usRg","ttfName":"Intrique Script Personal Use","style":"font-size:1.5em"},
                  {"name":"vonfontregular","ttfName":"VonFont","style":"font-size:2.2em;letter-spacing:-2px"},
                  {"name":"motion_picture_personal_useRg","ttfName":"Motion Picture Personal Use","style":"font-size:1.4em;letter-spacing:-2px"},
        
                  {"name":"retrohandretrohand","ttfName":"RETROHAND","style":"letter-spacing:-3px"}
                  ];
    var popularNames=["Hannah","Emily","Sarah","Madison","Brianna","Kaylee","Kaitlyn","Hailey","Alexis","Elizabeth","Taylor","Lauren","Ashley","Katherine","Jessica","Anna","Samantha","Makayla","Kayla","Madeline","Jasmine","Alyssa","Abigail","Olivia","Brittany","Nicole","Destiny","Mackenzie","Emma","Jennifer","Rachel","Sydney","Megan","Grace","Alexandra","Morgan","Savannah","Victoria","Sophia","Natalie","Amanda","Stephanie","Chloe","Allison","Rebecca","Jacqueline","Julia","Cheyenne","Amber","Erica","Isabella","Kylie","Christina","Brooke","Bailey","Maria","Diana","Danielle","Kelsey","Jordan","Andrea","Vanessa","Melissa","Kimberly","Sierra","Maya","Michelle","Caroline","Arianna","Zoe","Leslie","Isabel","Gabrielle","Faith","Lindsey","Erin","Kiara","Jenna","Casey","Paige","Mary","Alicia","Cameron","Alexandria","Molly","Melanie","Katie","Courtney","Trinity","Jada","Claire","Audrey","Adriana","Mia","Margaret","Riley","Jocelyn","Gabriela","Sabrina","Miranda"];

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


    function getChainCost() {
      var retVal = 0;
      if ($("input[type='radio'][name='chainLength']:checked").val() == "24") {
        retVal = 5;
      } 
      return retVal;
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
      $("#hPrice").val(total*100);
    }

    function updateOrderSubTotal() {
      $("#orderSubtotal").html("$" + getSubtotal());
    }

    function refreshExamples(doScroll) {

      var newText = $("#name").val();
      var newSize = 36;
      $(".preview").addClass("armedStyle");
      $(".sample").addClass("armedStyle");
      if (!newText || newText.length ==0) {
        newText = getNextPopularName();
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

      $(document).foundation();   
      $(document).ready(function() {

        //initSlick(); 
        $.each(fonts,function(index,val) {
          var id = val.name.replace(" ", "-");
          var fontSpacing = "";

          var scriptDiv = "<div  id='" 
                          + id 
                          + "' class='preview large-3 medium-4 small-6 columns' "
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
      refreshExamples();

        $(".preview").click(function() {
             timer.pause();
             if ($(this).hasClass("selectedScript")) {
               $(".selectedScript").removeClass("selectedScript");
               timer.play();
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
            $("#orderName").css('font-family',$(this).attr('id').replace("-"," "));
            $("#orderName").html($(this).html());
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
          $("#orderSummary").show();
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

        var timer = $.timer(function() {
          if ($("#name").val().length == 0) {
            refreshExamples();
          }
        },5000,true);    

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


  });
