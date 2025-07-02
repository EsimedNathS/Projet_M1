<h2>Bonjour {{ $user->name }},</h2>

<p>Voici la liste de vos factures en retard :</p>

<ul>
@foreach ($expenses as $expense)
    <li>
        <strong>Facture n°{{ $expense->expense_number }}</strong> - 
        Montant : {{ number_format($expense->calculateAmount(), 2, ',', ' ') }} € - 
        Échéance : {{ \Carbon\Carbon::parse($expense->date_payment_limit)->format('d/m/Y') }}
    </li>
@endforeach
</ul>

<p>Pensez à régulariser ces factures dès que possible.</p>
