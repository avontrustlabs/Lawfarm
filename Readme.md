## Solidity Code for Lawfarm Ethereum contract 

pragma solidity ^0.4.11;

contract Lawfarm { uint public _totalSupply = 1500000000000000000000000000;

string public constant symbol = "LAW"; string public constant name = "LawfarmToken"; uint8 public constant decimals = 18;

address public owner; bool public freeTransfer = false; mapping (address => uint256) balances; mapping (address => mapping (address => uint256)) allowed;

function Lawfarm (address _multisig) { balances[_multisig] = _totalSupply; owner = _multisig; }

modifier onlyOwner() { require(msg.sender == owner); _; }

modifier ownerOrEnabledTransfer() { require(freeTransfer || msg.sender == owner); _; }

function enableTransfer() onlyOwner() { freeTransfer = true; } function disableTransfer() onlyOwner() { freeTransfer = false; }

function totalSupply() constant returns (uint256 totalSupply){ return _totalSupply; }

function balanceOf(address _owner) constant returns (uint256 balance) { return balances[_owner]; }

function transfer(address _to, uint256 _value) ownerOrEnabledTransfer public returns (bool) { require( balances[msg.sender]>= _value && _value > 0 ); balances[msg.sender] -= _value; balances[_to] += _value; Transfer(msg.sender, _to, _value); return true; } function transferFrom(address _from, address _to, uint256 _value) ownerOrEnabledTransfer public returns (bool success) { require( allowed[_from][msg.sender] >= _value && balances[_from] >= _value && _value > 0 ); balances[_from] -= _value; balances[_to] += _value; allowed[_from][msg.sender] -= _value; Transfer(_from, _to, _value); return true; } function approve(address _spender, uint256 _value) public returns (bool success) { // To change the approve amount you first have to reduce the addresses// allowance to zero by callingapprove(_spender, 0)` if it is not // already 0 to mitigate the race condition described here: // https://github.com/ethereum/EIPs/issues/20#issuecomment-263524729 require(_value == 0 || allowed[msg.sender][_spender] == 0); allowed[msg.sender][_spender] = _value; Approval(msg.sender, _spender, _value); return true; } function allowance(address _owner, address _spender) constant returns (uint256 remaining) { return allowed[_owner][_spender]; } function transferOwnership(address newOwner) public onlyOwner returns (bool) { require(newOwner != address(0)); owner = newOwner; } 

event Approval(address indexed _owner, address indexed _spender, uint256 _value); event Transfer(address indexed _from, address indexed _to, uint256 _value); }

## ABI
[ { "anonymous": false, "inputs": [ { "indexed": true, "name": "_from", "type": "address" }, { "indexed": true, "name": "_to", "type": "address" }, { "indexed": false, "name": "_value", "type": "uint256" } ], "name": "Transfer", "type": "event" }, { "constant": false, "inputs": [ { "name": "_spender", "type": "address" }, { "name": "_value", "type": "uint256" } ], "name": "approve", "outputs": [ { "name": "success", "type": "bool" } ], "payable": false, "stateMutability": "nonpayable", "type": "function" }, { "constant": false, "inputs": [], "name": "disableTransfer", "outputs": [], "payable": false, "stateMutability": "nonpayable", "type": "function" }, { "constant": false, "inputs": [], "name": "enableTransfer", "outputs": [], "payable": false, "stateMutability": "nonpayable", "type": "function" }, { "constant": false, "inputs": [ { "name": "_to", "type": "address" }, { "name": "_value", "type": "uint256" } ], "name": "transfer", "outputs": [ { "name": "", "type": "bool" } ], "payable": false, "stateMutability": "nonpayable", "type": "function" }, { "constant": false, "inputs": [ { "name": "_from", "type": "address" }, { "name": "_to", "type": "address" }, { "name": "_value", "type": "uint256" } ], "name": "transferFrom", "outputs": [ { "name": "success", "type": "bool" } ], "payable": false, "stateMutability": "nonpayable", "type": "function" }, { "constant": false, "inputs": [ { "name": "newOwner", "type": "address" } ], "name": "transferOwnership", "outputs": [ { "name": "", "type": "bool" } ], "payable": false, "stateMutability": "nonpayable", "type": "function" }, { "anonymous": false, "inputs": [ { "indexed": true, "name": "_owner", "type": "address" }, { "indexed": true, "name": "_spender", "type": "address" }, { "indexed": false, "name": "_value", "type": "uint256" } ], "name": "Approval", "type": "event" }, { "inputs": [ { "name": "_multisig", "type": "address" } ], "payable": false, "stateMutability": "nonpayable", "type": "constructor" }, { "constant": true, "inputs": [], "name": "_totalSupply", "outputs": [ { "name": "", "type": "uint256" } ], "payable": false, "stateMutability": "view", "type": "function" }, { "constant": true, "inputs": [ { "name": "_owner", "type": "address" }, { "name": "_spender", "type": "address" } ], "name": "allowance", "outputs": [ { "name": "remaining", "type": "uint256" } ], "payable": false, "stateMutability": "view", "type": "function" }, { "constant": true, "inputs": [ { "name": "_owner", "type": "address" } ], "name": "balanceOf", "outputs": [ { "name": "balance", "type": "uint256" } ], "payable": false, "stateMutability": "view", "type": "function" }, { "constant": true, "inputs": [], "name": "decimals", "outputs": [ { "name": "", "type": "uint8" } ], "payable": false, "stateMutability": "view", "type": "function" }, { "constant": true, "inputs": [], "name": "freeTransfer", "outputs": [ { "name": "", "type": "bool" } ], "payable": false, "stateMutability": "view", "type": "function" }, { "constant": true, "inputs": [], "name": "name", "outputs": [ { "name": "", "type": "string" } ], "payable": false, "stateMutability": "view", "type": "function" }, { "constant": true, "inputs": [], "name": "owner", "outputs": [ { "name": "", "type": "address" } ], "payable": false, "stateMutability": "view", "type": "function" }, { "constant": true, "inputs": [], "name": "symbol", "outputs": [ { "name": "", "type": "string" } ], "payable": false, "stateMutability": "view", "type": "function" }, { "constant": true, "inputs": [], "name": "totalSupply", "outputs": [ { "name": "totalSupply", "type": "uint256" } ], "payable": false, "stateMutability": "view", "type": "function" } ]









## PHP Library for interacting with the Stellar network.

* Communicate with Horizon server
* Build and sign transactions

## :warning: Danger Zone :warning:

**Development Status**

This library is under active development and should be considered beta quality.
Please ensure that you've tested extensively on a test network and have added
sanity checks in other places in your code.

:warning: [See the 0.2.0 release notes for breaking changes](CHANGELOG.md) 

**Large Integer Support**

The largest PHP integer is 64-bits when on a 64-bit platform. This is especially
important to pay attention to when working with large balance transfers. The native
representation of a single XLM (1 XLM) is 10000000 stroops.

Therefore, if you try to use a `MAX_INT` number of XLM (or a custom asset) it is
possible to overflow PHP's integer type when the value is converted to stroops and
sent to the network.

This library attempts to add checks for this scenario and also uses a `BigInteger`
class to work around this problem.

If your application uses large amounts of XLM or a custom asset please do extensive
testing with large values and use the `StellarAmount` helper class or the `BigInteger` 
class if possible.

**Floating point issues**

Although not specific to Stellar or PHP, it's important to be aware of problems
when doing comparisons between floating point numbers.

For example:

```php
$oldBalance = 1.605;
$newBalance = 1.61;

var_dump($oldBalance + 0.005);
var_dump($newBalance);
if ($oldBalance + 0.005 === $newBalance) {
    print "Equal\n";
}
else {
    print "Not Equal\n";
}
```

The above code considers the two values not to be equal even though the same value
is printed out:

Output:
```
float(1.61)
float(1.61)
Not Equal
```

To work around this issue, always work with and store amounts as an integer representing stroops. Only convert
back to a decimal number when you need to display a balance to the user.

The static `StellarAmount::STROOP_SCALE` property can be used to help with this conversion.

## Getting Started

See the [getting-started](getting-started/) directory for examples of how to use this library.

These examples are modeled after the ones in Stellar's getting started guide:

https://www.stellar.org/developers/guides/get-started/create-account.html

Additional examples are available in the [examples](examples/) directory 

## Donations

Stellar: GCUVDZRQ6CX347AMUUWZDYSNDFAWDN6FUYM5DVYYVO574NHTAUCQAK53
