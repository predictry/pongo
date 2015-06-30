<div class="ibox float-e-margins"> 
    <div class="ibox-title">
        <h5>{{$tableHeader}} </h5>
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="fa fa-chevron-up"></i>
            </a>
        </div>
    </div>
    
    <div class="ibox-content">
        <table class="table table-striped">
            <thead>
              <tr>
                  <th>#ID</th>
                  <th>Name</th>
                  <th>Amount</th>
                  <th>Url</th>
              </tr>
            </thead>            
            <tbody> 
            @foreach($contents as $item)
              <tr>
                <td>{{ $item['id'] }}</td>
                <td><span class="line">{{ $item['name'] }}</span></td>
                <td>{{ $item['score'] }}</td>
                <td>{{ $item['url'] }}</td>
              </tr>     
            @endforeach
            </tbody>
        </table>
    </div>
</div>
