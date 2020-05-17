var tree = [
    {
      text: "Parent 1",
      nodes: [
        {
          text: "Child 1",
          nodes: [
            {
              text: "Grandchild 1"
            },
            {
              text: "Grandchild 2"
            }
          ]
        },
        {
          text: "Child 2"
        }
      ]
    },
    {
      text: "Parent 2"
    },
    {
      text: "Parent 3"
    },
    {
      text: "Parent 4"
    },
    {
      text: "Parent 5"
    }
  ];
// $('#tree').treeview({data: tree});

$(function(){
  baseUrl = 'mostrar_cta_contables';
  $.ajax({
      type: 'GET',
      // headers: {'X-CSRF-TOKEN': token},
      url: baseUrl,
      dataType: 'JSON',
      success: function(response){
        console.log(response);
        $('#tree').treeview({data: response});
      }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
});