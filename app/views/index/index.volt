{{ content() }}
    <div class="flash">
              {#% set userLogin= session.get('auth-front')%}
               {% if userLogin['username'] is defined%}
              <div class="alert alert-info">
                  You are currently logged in username: {{userLogin['username']}}
              </div>
              {% else %}
              <div class="alert alert-info">
                  You are currently not logged in. Please log in to see all your books
              </div>
              {% endif %#}
    </div>  
{% include 'layouts/baner.volt' %}
<div class="row">
  <!-- categories sidebar left -->
  <div class="span8">
      <div class="content">
        {% for post in page.items %}
          <h2>{{ link_to('view/' ~ post.id ~ '/' ~ post.slug, post.title|e) }}</h2>
          <span class="date">{{ date('M d/Y H:i',post.created) }}</span>
            <span> {{('By')}} <a href="">{{post.userPost.username}}</a>, {{t('Views')}}: {{post.numberViews}}
          </span>
          <div class="substr-posts">
          <?php echo substr($post->content,0, 360);?>
          </div>
          <div class="clear"></div>
              {% endfor %}
      </div>

  </div><!-- span8 -->
    {% include 'layouts/sidebar.volt'%}

</div><!-- end row -->
<div class="row">

<div class="span9"></div>
  <div class="span3">  
      <ul class="pager">
        <li>{{ link_to("index?page=" ~ page.before, '&larr; Older') }}
        </li>
        <li>{{ link_to("index?page=" ~ page.next, 'Newer &rarr;') }}</li>
      </ul>
  </div>
</div>