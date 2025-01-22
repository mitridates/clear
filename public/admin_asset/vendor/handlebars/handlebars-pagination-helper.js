/**
 * Pagination helper modificado
 * @author Chun-Yi Liu (trantorLiu)
 * @see https://gist.github.com/trantorLiu/5924389
 */
Handlebars.registerHelper('pagination', function(currentPage, totalPages, parameters, data, options) {
    var startPage, endPage, context, prop,
        props= {
            showAlwaysFirstAndLast: false,
            adjacents: 1,
            arround: 1,
            size: null
        };

    if(parameters){
        for (prop in parameters) {
            props[prop]= parameters[prop]
        }
    }

    // props['totalPages']= totalPages
    if(!props['size']) props['size'] = (props.adjacents*2)+ (props.arround*2) + 1
    startPage = currentPage - Math.floor(props.size / 2);
    endPage = currentPage + Math.floor(props.size / 2);

    if (startPage <= 0) {
        endPage -= (startPage - 1);
        startPage = 1;
    }

    if (endPage > totalPages) {
        endPage = totalPages;
        if (endPage - props.size + 1 > 0) {
            startPage = endPage - props.size + 1;
        } else {
            startPage = 1;
        }
    }

    context = {
        startFromFirstPage: false,
        pages: [],
        endAtLastPage: false,
        gotoEnd: false,
        gotoInit: false,
        next:  false,
        prev:  false,
        currentPage:  currentPage,
        props: props,
        data: data
    };
    if (startPage === 1) {
        context.startFromFirstPage = true;
    }else{
        context.prev=  currentPage-1
    }
    for (var i = startPage; i <= endPage; i++) {
        context.pages.push({
            page: i,
            isCurrent: i === currentPage,
        });
    }
    if (endPage === totalPages) {
        context.endAtLastPage = true;
    }else{
        context.next= currentPage+1
    }

    if((totalPages - currentPage) < (props.size/2)) context.gotoEnd= true
    if(currentPage-1 < (props.size/2)) context.gotoInit= true

    return options.fn(context);
});