<div class="billing-form-fields">
    <h3>Billing Info</h3>
    <div class="col-xs-12">
        <div class="form-group">
            {!! Form::label('billing_company_name', 'Company Name', array('class' => 'awesome')); !!}
            {!! Form::text('billing_company_name', null, array('id' => $type . '_billing_company_name', 'class' => 'form-control', ($required ? "required" : ""))); !!}
        </div>

        <div class="form-group">
            {!! Form::label('billing_registry', 'Registrul Comertului', array('class' => 'awesome')); !!}
            {!! Form::text('billing_registry', null, array('id' => $type . '_billing_registry', 'class' => 'form-control', ($required ? "required" : ""))); !!}
        </div>

        <div class="form-group">
            {!! Form::label('billing_cif', 'CIF', array('class' => 'awesome')); !!}
            {!! Form::text('billing_cif', null, array('id' => $type . '_billing_cif', 'class' => 'form-control', ($required ? "required" : ""))); !!}
        </div>

        <div class="form-group">
            {!! Form::label('billing_address', 'Adresa', array('class' => 'awesome')); !!}
            {!! Form::text('billing_address', null, array('id' => $type . '_billing_address', 'class' => 'form-control', ($required ? "required" : ""))); !!}
        </div>

        <div class="form-group">
            {!! Form::label('billing_iban', 'IBAN', array('class' => 'awesome')); !!}
            {!! Form::text('billing_iban', null, array('id' => $type . '_billing_iban', 'class' => 'form-control', ($required ? "required" : ""))); !!}
        </div>

        <div class="form-group">
            {!! Form::label('billing_bank', 'Banca', array('class' => 'awesome')); !!}
            {!! Form::text('billing_bank', null, array('id' => $type . '_billing_bank', 'class' => 'form-control', ($required ? "required" : ""))); !!}
        </div>

        <div class="form-group">
            {!! Form::label('billing_capital', 'Capital Social', array('class' => 'awesome')); !!}
            {!! Form::text('billing_capital', null, array('id' => $type . '_billing_capital', 'class' => 'form-control', ($required ? "required" : ""))); !!}
        </div>

        <div class="form-group">
            {!! Form::label('billing_contract_no', 'Contract nr.', array('class' => 'awesome')); !!}
            {!! Form::text('billing_contract_no', null, array('id' => $type . '_billing_contract_no', 'class' => 'form-control', ($required ? "required" : ""))); !!}
        </div>

        <div class="form-group">
            {!! Form::label('billing_contract_date', 'Data Contract', array('class' => 'awesome')); !!}
            {!! Form::text('billing_contract_date', null, array('id' => $type . '_billing_contract_date', 'class' => 'has-datepicker form-control', ($required ? "required" : ""))); !!}
        </div>

        @if ($type == "client" || $type == "client_edit")
            <div class="form-group">
                <p>
                    {!! Form::checkbox('billing_hidden', 1, false, array('id' => $type . '_billing_hidden')); !!}
                    {!! Form::label($type . '_billing_hidden', 'Hide the prices', array('class' => 'awesome')); !!}
                </p>
            </div>
        @endif
    </div>
</div>

