<span class="tm-pagination-label">第 {{ @$page_data['page'] }} / {{ @$page_data['last_page'] }} 頁 共 {{ @$page_data['count'] }} 筆</span>
<nav aria-label="Page navigation" class="d-inline-block">
    <ul class="pagination tm-pagination">
        <a class="page-link" onclick="changeForm('{{ @$page_data['page_link'] }}?page=1{{ @$page_data['search_get_url'] }}');"><i class="page-item fas fa-angle-double-left"></i></a>
        <a class="page-link" onclick="changeForm('{{ @$page_data['page_link'] }}?page={{ @$page_data['previous_page_number'] }}{{ @$page_data['search_get_url'] }}');"><i class="page-item fas fa-angle-left"></i></a>
        <a class="page-link" onclick="changeForm('{{ @$page_data['page_link'] }}?page={{ @$page_data['next_page_number'] }}{{ @$page_data['search_get_url'] }}');"><i class="page-item fas fa-angle-right"></i></a>
        <a class="page-link" onclick="changeForm('{{ @$page_data['page_link'] }}?page={{ @$page_data['last_page'] }}{{ @$page_data['search_get_url'] }}');"><i class="page-item fas fa-angle-double-right"></i></a>
    </ul>
</nav>