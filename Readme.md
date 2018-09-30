

pragma solidity ^0.4.24;

// ----------------------------------------------------------------------------
// Lawfarm token contract
//
// Symbol      : LAW
// Name        : Lawfarm
// Total supply: 150,000,000.000000000000000000
// Decimals    : 18
//
// Enjoy.
//
// (c) BokkyPooBah / Bok Consulting Pty Ltd 2018. The MIT Licence.
// ----------------------------------------------------------------------------


// ----------------------------------------------------------------------------
// Safe maths
// ----------------------------------------------------------------------------
library SafeMath {
    function add(uint a, uint b) internal pure returns (uint c) {
        c = a + b;
        require(c >= a);
    }
    function sub(uint a, uint b) internal pure returns (uint c) {
        require(b <= a);
        c = a - b;
    }
    function mul(uint a, uint b) internal pure returns (uint c) {
        c = a * b;
        require(a == 0 || c / a == b);
    }
    function div(uint a, uint b) internal pure returns (uint c) {
        require(b > 0);
        c = a / b;
    }
}


// ----------------------------------------------------------------------------
// ERC Token Standard #20 Interface
// https://github.com/ethereum/EIPs/blob/master/EIPS/eip-20.md
// ----------------------------------------------------------------------------
contract ERC20Interface {
    function totalSupply() public constant returns (uint);
    function balanceOf(address tokenOwner) public constant returns (uint balance);
    function allowance(address tokenOwner, address spender) public constant returns (uint remaining);
    function transfer(address to, uint tokens) public returns (bool success);
    function approve(address spender, uint tokens) public returns (bool success);
    function transferFrom(address from, address to, uint tokens) public returns (bool success);

    event Transfer(address indexed from, address indexed to, uint tokens);
    event Approval(address indexed tokenOwner, address indexed spender, uint tokens);
}


// ----------------------------------------------------------------------------
// Contract function to receive approval and execute function in one call
//
// Borrowed from MiniMeToken
// ----------------------------------------------------------------------------
contract ApproveAndCallFallBack {
    function receiveApproval(address from, uint256 tokens, address token, bytes data) public;
}


// ----------------------------------------------------------------------------
// Owned contract
// ----------------------------------------------------------------------------
contract Owned {
    address public owner;
    address public newOwner;

    event OwnershipTransferred(address indexed _from, address indexed _to);

    constructor() public {
        owner = msg.sender;
    }

    modifier onlyOwner {
        require(msg.sender == owner);
        _;
    }

    function transferOwnership(address _newOwner) public onlyOwner {
        newOwner = _newOwner;
    }
    function acceptOwnership() public {
        require(msg.sender == newOwner);
        emit OwnershipTransferred(owner, newOwner);
        owner = newOwner;
        newOwner = address(0);
    }
}


// ----------------------------------------------------------------------------
// ERC20 Token, with the addition of symbol, name and decimals and a
// fixed supply
// ----------------------------------------------------------------------------
contract LawfarmToken is ERC20Interface, Owned {
    using SafeMath for uint;

    string public symbol;
    string public  name;
    uint8 public decimals;
    uint _totalSupply;

    mapping(address => uint) balances;
    mapping(address => mapping(address => uint)) allowed;


    // ------------------------------------------------------------------------
    // Constructor
    // ------------------------------------------------------------------------
    constructor() public {
        symbol = "LAW";
        name = "Lawfarm Token v1";
        decimals = 18;
        _totalSupply = 150000000 * 10**uint(decimals);
        balances[owner] = _totalSupply;
        emit Transfer(address(0), owner, _totalSupply);
    }


    // ------------------------------------------------------------------------
    // Total supply
    // ------------------------------------------------------------------------
    function totalSupply() public view returns (uint) {
        return _totalSupply.sub(balances[address(0)]);
    }


    // ------------------------------------------------------------------------
    // Get the token balance for account `tokenOwner`
    // ------------------------------------------------------------------------
    function balanceOf(address tokenOwner) public view returns (uint balance) {
        return balances[tokenOwner];
    }


    // ------------------------------------------------------------------------
    // Transfer the balance from token owner's account to `to` account
    // - Owner's account must have sufficient balance to transfer
    // - 0 value transfers are allowed
    // ------------------------------------------------------------------------
    function transfer(address to, uint tokens) public returns (bool success) {
        balances[msg.sender] = balances[msg.sender].sub(tokens);
        balances[to] = balances[to].add(tokens);
        emit Transfer(msg.sender, to, tokens);
        return true;
    }


    // ------------------------------------------------------------------------
    // Token owner can approve for `spender` to transferFrom(...) `tokens`
    // from the token owner's account
    //
    // https://github.com/ethereum/EIPs/blob/master/EIPS/eip-20-token-standard.md
    // recommends that there are no checks for the approval double-spend attack
    // as this should be implemented in user interfaces 
    // ------------------------------------------------------------------------
    function approve(address spender, uint tokens) public returns (bool success) {
        allowed[msg.sender][spender] = tokens;
        emit Approval(msg.sender, spender, tokens);
        return true;
    }


    // ------------------------------------------------------------------------
    // Transfer `tokens` from the `from` account to the `to` account
    // 
    // The calling account must already have sufficient tokens approve(...)-d
    // for spending from the `from` account and
    // - From account must have sufficient balance to transfer
    // - Spender must have sufficient allowance to transfer
    // - 0 value transfers are allowed
    // ------------------------------------------------------------------------
    function transferFrom(address from, address to, uint tokens) public returns (bool success) {
        balances[from] = balances[from].sub(tokens);
        allowed[from][msg.sender] = allowed[from][msg.sender].sub(tokens);
        balances[to] = balances[to].add(tokens);
        emit Transfer(from, to, tokens);
        return true;
    }


    // ------------------------------------------------------------------------
    // Returns the amount of tokens approved by the owner that can be
    // transferred to the spender's account
    // ------------------------------------------------------------------------
    function allowance(address tokenOwner, address spender) public view returns (uint remaining) {
        return allowed[tokenOwner][spender];
    }


    // ------------------------------------------------------------------------
    // Token owner can approve for `spender` to transferFrom(...) `tokens`
    // from the token owner's account. The `spender` contract function
    // `receiveApproval(...)` is then executed
    // ------------------------------------------------------------------------
    function approveAndCall(address spender, uint tokens, bytes data) public returns (bool success) {
        allowed[msg.sender][spender] = tokens;
        emit Approval(msg.sender, spender, tokens);
        ApproveAndCallFallBack(spender).receiveApproval(msg.sender, tokens, this, data);
        return true;
    }


    // ------------------------------------------------------------------------
    // Don't accept ETH
    // ------------------------------------------------------------------------
    function () public payable {
        revert();
    }


    // ------------------------------------------------------------------------
    // Owner can transfer out any accidentally sent ERC20 tokens
    // ------------------------------------------------------------------------
    function transferAnyERC20Token(address tokenAddress, uint tokens) public onlyOwner returns (bool success) {
        return ERC20Interface(tokenAddress).transfer(owner, tokens);
    }
}


{
	"linkReferences": {},
	"object": "608060405234801561001057600080fd5b5060008054600160a060020a031916331790556040805180820190915260038082527f4c4157000000000000000000000000000000000000000000000000000000000060209092019182526100679160029161012b565b506040805180820190915260108082527f4c61776661726d20546f6b656e2076310000000000000000000000000000000060209092019182526100ac9160039161012b565b5060048054601260ff19909116179081905560ff16600a0a6308f0d18002600581905560008054600160a060020a0390811682526006602090815260408084208590558354815195865290519216937fddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef929081900390910190a36101c6565b828054600181600116156101000203166002900490600052602060002090601f016020900481019282601f1061016c57805160ff1916838001178555610199565b82800160010185558215610199579182015b8281111561019957825182559160200191906001019061017e565b506101a59291506101a9565b5090565b6101c391905b808211156101a557600081556001016101af565b90565b610a9c806101d56000396000f3006080604052600436106100da5763ffffffff7c010000000000000000000000000000000000000000000000000000000060003504166306fdde0381146100df578063095ea7b31461016957806318160ddd146101a157806323b872dd146101c8578063313ce567146101f257806370a082311461021d57806379ba50971461023e5780638da5cb5b1461025557806395d89b4114610286578063a9059cbb1461029b578063cae9ca51146102bf578063d4ee1d9014610328578063dc39d06d1461033d578063dd62ed3e14610361578063f2fde38b14610388575b600080fd5b3480156100eb57600080fd5b506100f46103a9565b6040805160208082528351818301528351919283929083019185019080838360005b8381101561012e578181015183820152602001610116565b50505050905090810190601f16801561015b5780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b34801561017557600080fd5b5061018d600160a060020a0360043516602435610437565b604080519115158252519081900360200190f35b3480156101ad57600080fd5b506101b661049e565b60408051918252519081900360200190f35b3480156101d457600080fd5b5061018d600160a060020a03600435811690602435166044356104e1565b3480156101fe57600080fd5b506102076105ec565b6040805160ff9092168252519081900360200190f35b34801561022957600080fd5b506101b6600160a060020a03600435166105f5565b34801561024a57600080fd5b50610253610610565b005b34801561026157600080fd5b5061026a610698565b60408051600160a060020a039092168252519081900360200190f35b34801561029257600080fd5b506100f46106a7565b3480156102a757600080fd5b5061018d600160a060020a03600435166024356106ff565b3480156102cb57600080fd5b50604080516020600460443581810135601f810184900484028501840190955284845261018d948235600160a060020a03169460248035953695946064949201919081908401838280828437509497506107af9650505050505050565b34801561033457600080fd5b5061026a610910565b34801561034957600080fd5b5061018d600160a060020a036004351660243561091f565b34801561036d57600080fd5b506101b6600160a060020a03600435811690602435166109da565b34801561039457600080fd5b50610253600160a060020a0360043516610a05565b6003805460408051602060026001851615610100026000190190941693909304601f8101849004840282018401909252818152929183018282801561042f5780601f106104045761010080835404028352916020019161042f565b820191906000526020600020905b81548152906001019060200180831161041257829003601f168201915b505050505081565b336000818152600760209081526040808320600160a060020a038716808552908352818420869055815186815291519394909390927f8c5be1e5ebec7d5bd14f71427d1e84f3dd0314c0f7b2291e5b200ac8c7c3b925928290030190a35060015b92915050565b600080805260066020527f54cdd369e4e8a8515e52ca72ec816c2101831ad1f18bf44102ed171459c9b4f8546005546104dc9163ffffffff610a4b16565b905090565b600160a060020a03831660009081526006602052604081205461050a908363ffffffff610a4b16565b600160a060020a0385166000908152600660209081526040808320939093556007815282822033835290522054610547908363ffffffff610a4b16565b600160a060020a03808616600090815260076020908152604080832033845282528083209490945591861681526006909152205461058b908363ffffffff610a6016565b600160a060020a0380851660008181526006602090815260409182902094909455805186815290519193928816927fddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef92918290030190a35060019392505050565b60045460ff1681565b600160a060020a031660009081526006602052604090205490565b600154600160a060020a0316331461062757600080fd5b60015460008054604051600160a060020a0393841693909116917f8be0079c531659141344cd1fd0a4f28419497f9722a3daafe3b4186f6b6457e091a3600180546000805473ffffffffffffffffffffffffffffffffffffffff19908116600160a060020a03841617909155169055565b600054600160a060020a031681565b6002805460408051602060018416156101000260001901909316849004601f8101849004840282018401909252818152929183018282801561042f5780601f106104045761010080835404028352916020019161042f565b3360009081526006602052604081205461071f908363ffffffff610a4b16565b3360009081526006602052604080822092909255600160a060020a03851681522054610751908363ffffffff610a6016565b600160a060020a0384166000818152600660209081526040918290209390935580518581529051919233927fddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef9281900390910190a350600192915050565b336000818152600760209081526040808320600160a060020a038816808552908352818420879055815187815291519394909390927f8c5be1e5ebec7d5bd14f71427d1e84f3dd0314c0f7b2291e5b200ac8c7c3b925928290030190a36040517f8f4ffcb10000000000000000000000000000000000000000000000000000000081523360048201818152602483018690523060448401819052608060648501908152865160848601528651600160a060020a038a1695638f4ffcb195948a94938a939192909160a490910190602085019080838360005b8381101561089f578181015183820152602001610887565b50505050905090810190601f1680156108cc5780820380516001836020036101000a031916815260200191505b5095505050505050600060405180830381600087803b1580156108ee57600080fd5b505af1158015610902573d6000803e3d6000fd5b506001979650505050505050565b600154600160a060020a031681565b60008054600160a060020a0316331461093757600080fd5b60008054604080517fa9059cbb000000000000000000000000000000000000000000000000000000008152600160a060020a0392831660048201526024810186905290519186169263a9059cbb926044808401936020939083900390910190829087803b1580156109a757600080fd5b505af11580156109bb573d6000803e3d6000fd5b505050506040513d60208110156109d157600080fd5b50519392505050565b600160a060020a03918216600090815260076020908152604080832093909416825291909152205490565b600054600160a060020a03163314610a1c57600080fd5b6001805473ffffffffffffffffffffffffffffffffffffffff1916600160a060020a0392909216919091179055565b600082821115610a5a57600080fd5b50900390565b8181018281101561049857600080fd00a165627a7a723058201a25e9b3d915e94e59e4364c5233967e6bb103e29668744f14e9086f65526cfc0029",
	"opcodes": "PUSH1 0x80 PUSH1 0x40 MSTORE CALLVALUE DUP1 ISZERO PUSH2 0x10 JUMPI PUSH1 0x0 DUP1 REVERT JUMPDEST POP PUSH1 0x0 DUP1 SLOAD PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB NOT AND CALLER OR SWAP1 SSTORE PUSH1 0x40 DUP1 MLOAD DUP1 DUP3 ADD SWAP1 SWAP2 MSTORE PUSH1 0x3 DUP1 DUP3 MSTORE PUSH32 0x4C41570000000000000000000000000000000000000000000000000000000000 PUSH1 0x20 SWAP1 SWAP3 ADD SWAP2 DUP3 MSTORE PUSH2 0x67 SWAP2 PUSH1 0x2 SWAP2 PUSH2 0x12B JUMP JUMPDEST POP PUSH1 0x40 DUP1 MLOAD DUP1 DUP3 ADD SWAP1 SWAP2 MSTORE PUSH1 0x10 DUP1 DUP3 MSTORE PUSH32 0x4C61776661726D20546F6B656E20763100000000000000000000000000000000 PUSH1 0x20 SWAP1 SWAP3 ADD SWAP2 DUP3 MSTORE PUSH2 0xAC SWAP2 PUSH1 0x3 SWAP2 PUSH2 0x12B JUMP JUMPDEST POP PUSH1 0x4 DUP1 SLOAD PUSH1 0x12 PUSH1 0xFF NOT SWAP1 SWAP2 AND OR SWAP1 DUP2 SWAP1 SSTORE PUSH1 0xFF AND PUSH1 0xA EXP PUSH4 0x8F0D180 MUL PUSH1 0x5 DUP2 SWAP1 SSTORE PUSH1 0x0 DUP1 SLOAD PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB SWAP1 DUP2 AND DUP3 MSTORE PUSH1 0x6 PUSH1 0x20 SWAP1 DUP2 MSTORE PUSH1 0x40 DUP1 DUP5 KECCAK256 DUP6 SWAP1 SSTORE DUP4 SLOAD DUP2 MLOAD SWAP6 DUP7 MSTORE SWAP1 MLOAD SWAP3 AND SWAP4 PUSH32 0xDDF252AD1BE2C89B69C2B068FC378DAA952BA7F163C4A11628F55A4DF523B3EF SWAP3 SWAP1 DUP2 SWAP1 SUB SWAP1 SWAP2 ADD SWAP1 LOG3 PUSH2 0x1C6 JUMP JUMPDEST DUP3 DUP1 SLOAD PUSH1 0x1 DUP2 PUSH1 0x1 AND ISZERO PUSH2 0x100 MUL SUB AND PUSH1 0x2 SWAP1 DIV SWAP1 PUSH1 0x0 MSTORE PUSH1 0x20 PUSH1 0x0 KECCAK256 SWAP1 PUSH1 0x1F ADD PUSH1 0x20 SWAP1 DIV DUP2 ADD SWAP3 DUP3 PUSH1 0x1F LT PUSH2 0x16C JUMPI DUP1 MLOAD PUSH1 0xFF NOT AND DUP4 DUP1 ADD OR DUP6 SSTORE PUSH2 0x199 JUMP JUMPDEST DUP3 DUP1 ADD PUSH1 0x1 ADD DUP6 SSTORE DUP3 ISZERO PUSH2 0x199 JUMPI SWAP2 DUP3 ADD JUMPDEST DUP3 DUP2 GT ISZERO PUSH2 0x199 JUMPI DUP3 MLOAD DUP3 SSTORE SWAP2 PUSH1 0x20 ADD SWAP2 SWAP1 PUSH1 0x1 ADD SWAP1 PUSH2 0x17E JUMP JUMPDEST POP PUSH2 0x1A5 SWAP3 SWAP2 POP PUSH2 0x1A9 JUMP JUMPDEST POP SWAP1 JUMP JUMPDEST PUSH2 0x1C3 SWAP2 SWAP1 JUMPDEST DUP1 DUP3 GT ISZERO PUSH2 0x1A5 JUMPI PUSH1 0x0 DUP2 SSTORE PUSH1 0x1 ADD PUSH2 0x1AF JUMP JUMPDEST SWAP1 JUMP JUMPDEST PUSH2 0xA9C DUP1 PUSH2 0x1D5 PUSH1 0x0 CODECOPY PUSH1 0x0 RETURN STOP PUSH1 0x80 PUSH1 0x40 MSTORE PUSH1 0x4 CALLDATASIZE LT PUSH2 0xDA JUMPI PUSH4 0xFFFFFFFF PUSH29 0x100000000000000000000000000000000000000000000000000000000 PUSH1 0x0 CALLDATALOAD DIV AND PUSH4 0x6FDDE03 DUP2 EQ PUSH2 0xDF JUMPI DUP1 PUSH4 0x95EA7B3 EQ PUSH2 0x169 JUMPI DUP1 PUSH4 0x18160DDD EQ PUSH2 0x1A1 JUMPI DUP1 PUSH4 0x23B872DD EQ PUSH2 0x1C8 JUMPI DUP1 PUSH4 0x313CE567 EQ PUSH2 0x1F2 JUMPI DUP1 PUSH4 0x70A08231 EQ PUSH2 0x21D JUMPI DUP1 PUSH4 0x79BA5097 EQ PUSH2 0x23E JUMPI DUP1 PUSH4 0x8DA5CB5B EQ PUSH2 0x255 JUMPI DUP1 PUSH4 0x95D89B41 EQ PUSH2 0x286 JUMPI DUP1 PUSH4 0xA9059CBB EQ PUSH2 0x29B JUMPI DUP1 PUSH4 0xCAE9CA51 EQ PUSH2 0x2BF JUMPI DUP1 PUSH4 0xD4EE1D90 EQ PUSH2 0x328 JUMPI DUP1 PUSH4 0xDC39D06D EQ PUSH2 0x33D JUMPI DUP1 PUSH4 0xDD62ED3E EQ PUSH2 0x361 JUMPI DUP1 PUSH4 0xF2FDE38B EQ PUSH2 0x388 JUMPI JUMPDEST PUSH1 0x0 DUP1 REVERT JUMPDEST CALLVALUE DUP1 ISZERO PUSH2 0xEB JUMPI PUSH1 0x0 DUP1 REVERT JUMPDEST POP PUSH2 0xF4 PUSH2 0x3A9 JUMP JUMPDEST PUSH1 0x40 DUP1 MLOAD PUSH1 0x20 DUP1 DUP3 MSTORE DUP4 MLOAD DUP2 DUP4 ADD MSTORE DUP4 MLOAD SWAP2 SWAP3 DUP4 SWAP3 SWAP1 DUP4 ADD SWAP2 DUP6 ADD SWAP1 DUP1 DUP4 DUP4 PUSH1 0x0 JUMPDEST DUP4 DUP2 LT ISZERO PUSH2 0x12E JUMPI DUP2 DUP2 ADD MLOAD DUP4 DUP3 ADD MSTORE PUSH1 0x20 ADD PUSH2 0x116 JUMP JUMPDEST POP POP POP POP SWAP1 POP SWAP1 DUP2 ADD SWAP1 PUSH1 0x1F AND DUP1 ISZERO PUSH2 0x15B JUMPI DUP1 DUP3 SUB DUP1 MLOAD PUSH1 0x1 DUP4 PUSH1 0x20 SUB PUSH2 0x100 EXP SUB NOT AND DUP2 MSTORE PUSH1 0x20 ADD SWAP2 POP JUMPDEST POP SWAP3 POP POP POP PUSH1 0x40 MLOAD DUP1 SWAP2 SUB SWAP1 RETURN JUMPDEST CALLVALUE DUP1 ISZERO PUSH2 0x175 JUMPI PUSH1 0x0 DUP1 REVERT JUMPDEST POP PUSH2 0x18D PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB PUSH1 0x4 CALLDATALOAD AND PUSH1 0x24 CALLDATALOAD PUSH2 0x437 JUMP JUMPDEST PUSH1 0x40 DUP1 MLOAD SWAP2 ISZERO ISZERO DUP3 MSTORE MLOAD SWAP1 DUP2 SWAP1 SUB PUSH1 0x20 ADD SWAP1 RETURN JUMPDEST CALLVALUE DUP1 ISZERO PUSH2 0x1AD JUMPI PUSH1 0x0 DUP1 REVERT JUMPDEST POP PUSH2 0x1B6 PUSH2 0x49E JUMP JUMPDEST PUSH1 0x40 DUP1 MLOAD SWAP2 DUP3 MSTORE MLOAD SWAP1 DUP2 SWAP1 SUB PUSH1 0x20 ADD SWAP1 RETURN JUMPDEST CALLVALUE DUP1 ISZERO PUSH2 0x1D4 JUMPI PUSH1 0x0 DUP1 REVERT JUMPDEST POP PUSH2 0x18D PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB PUSH1 0x4 CALLDATALOAD DUP2 AND SWAP1 PUSH1 0x24 CALLDATALOAD AND PUSH1 0x44 CALLDATALOAD PUSH2 0x4E1 JUMP JUMPDEST CALLVALUE DUP1 ISZERO PUSH2 0x1FE JUMPI PUSH1 0x0 DUP1 REVERT JUMPDEST POP PUSH2 0x207 PUSH2 0x5EC JUMP JUMPDEST PUSH1 0x40 DUP1 MLOAD PUSH1 0xFF SWAP1 SWAP3 AND DUP3 MSTORE MLOAD SWAP1 DUP2 SWAP1 SUB PUSH1 0x20 ADD SWAP1 RETURN JUMPDEST CALLVALUE DUP1 ISZERO PUSH2 0x229 JUMPI PUSH1 0x0 DUP1 REVERT JUMPDEST POP PUSH2 0x1B6 PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB PUSH1 0x4 CALLDATALOAD AND PUSH2 0x5F5 JUMP JUMPDEST CALLVALUE DUP1 ISZERO PUSH2 0x24A JUMPI PUSH1 0x0 DUP1 REVERT JUMPDEST POP PUSH2 0x253 PUSH2 0x610 JUMP JUMPDEST STOP JUMPDEST CALLVALUE DUP1 ISZERO PUSH2 0x261 JUMPI PUSH1 0x0 DUP1 REVERT JUMPDEST POP PUSH2 0x26A PUSH2 0x698 JUMP JUMPDEST PUSH1 0x40 DUP1 MLOAD PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB SWAP1 SWAP3 AND DUP3 MSTORE MLOAD SWAP1 DUP2 SWAP1 SUB PUSH1 0x20 ADD SWAP1 RETURN JUMPDEST CALLVALUE DUP1 ISZERO PUSH2 0x292 JUMPI PUSH1 0x0 DUP1 REVERT JUMPDEST POP PUSH2 0xF4 PUSH2 0x6A7 JUMP JUMPDEST CALLVALUE DUP1 ISZERO PUSH2 0x2A7 JUMPI PUSH1 0x0 DUP1 REVERT JUMPDEST POP PUSH2 0x18D PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB PUSH1 0x4 CALLDATALOAD AND PUSH1 0x24 CALLDATALOAD PUSH2 0x6FF JUMP JUMPDEST CALLVALUE DUP1 ISZERO PUSH2 0x2CB JUMPI PUSH1 0x0 DUP1 REVERT JUMPDEST POP PUSH1 0x40 DUP1 MLOAD PUSH1 0x20 PUSH1 0x4 PUSH1 0x44 CALLDATALOAD DUP2 DUP2 ADD CALLDATALOAD PUSH1 0x1F DUP2 ADD DUP5 SWAP1 DIV DUP5 MUL DUP6 ADD DUP5 ADD SWAP1 SWAP6 MSTORE DUP5 DUP5 MSTORE PUSH2 0x18D SWAP5 DUP3 CALLDATALOAD PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB AND SWAP5 PUSH1 0x24 DUP1 CALLDATALOAD SWAP6 CALLDATASIZE SWAP6 SWAP5 PUSH1 0x64 SWAP5 SWAP3 ADD SWAP2 SWAP1 DUP2 SWAP1 DUP5 ADD DUP4 DUP3 DUP1 DUP3 DUP5 CALLDATACOPY POP SWAP5 SWAP8 POP PUSH2 0x7AF SWAP7 POP POP POP POP POP POP POP JUMP JUMPDEST CALLVALUE DUP1 ISZERO PUSH2 0x334 JUMPI PUSH1 0x0 DUP1 REVERT JUMPDEST POP PUSH2 0x26A PUSH2 0x910 JUMP JUMPDEST CALLVALUE DUP1 ISZERO PUSH2 0x349 JUMPI PUSH1 0x0 DUP1 REVERT JUMPDEST POP PUSH2 0x18D PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB PUSH1 0x4 CALLDATALOAD AND PUSH1 0x24 CALLDATALOAD PUSH2 0x91F JUMP JUMPDEST CALLVALUE DUP1 ISZERO PUSH2 0x36D JUMPI PUSH1 0x0 DUP1 REVERT JUMPDEST POP PUSH2 0x1B6 PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB PUSH1 0x4 CALLDATALOAD DUP2 AND SWAP1 PUSH1 0x24 CALLDATALOAD AND PUSH2 0x9DA JUMP JUMPDEST CALLVALUE DUP1 ISZERO PUSH2 0x394 JUMPI PUSH1 0x0 DUP1 REVERT JUMPDEST POP PUSH2 0x253 PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB PUSH1 0x4 CALLDATALOAD AND PUSH2 0xA05 JUMP JUMPDEST PUSH1 0x3 DUP1 SLOAD PUSH1 0x40 DUP1 MLOAD PUSH1 0x20 PUSH1 0x2 PUSH1 0x1 DUP6 AND ISZERO PUSH2 0x100 MUL PUSH1 0x0 NOT ADD SWAP1 SWAP5 AND SWAP4 SWAP1 SWAP4 DIV PUSH1 0x1F DUP2 ADD DUP5 SWAP1 DIV DUP5 MUL DUP3 ADD DUP5 ADD SWAP1 SWAP3 MSTORE DUP2 DUP2 MSTORE SWAP3 SWAP2 DUP4 ADD DUP3 DUP3 DUP1 ISZERO PUSH2 0x42F JUMPI DUP1 PUSH1 0x1F LT PUSH2 0x404 JUMPI PUSH2 0x100 DUP1 DUP4 SLOAD DIV MUL DUP4 MSTORE SWAP2 PUSH1 0x20 ADD SWAP2 PUSH2 0x42F JUMP JUMPDEST DUP3 ADD SWAP2 SWAP1 PUSH1 0x0 MSTORE PUSH1 0x20 PUSH1 0x0 KECCAK256 SWAP1 JUMPDEST DUP2 SLOAD DUP2 MSTORE SWAP1 PUSH1 0x1 ADD SWAP1 PUSH1 0x20 ADD DUP1 DUP4 GT PUSH2 0x412 JUMPI DUP3 SWAP1 SUB PUSH1 0x1F AND DUP3 ADD SWAP2 JUMPDEST POP POP POP POP POP DUP2 JUMP JUMPDEST CALLER PUSH1 0x0 DUP2 DUP2 MSTORE PUSH1 0x7 PUSH1 0x20 SWAP1 DUP2 MSTORE PUSH1 0x40 DUP1 DUP4 KECCAK256 PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB DUP8 AND DUP1 DUP6 MSTORE SWAP1 DUP4 MSTORE DUP2 DUP5 KECCAK256 DUP7 SWAP1 SSTORE DUP2 MLOAD DUP7 DUP2 MSTORE SWAP2 MLOAD SWAP4 SWAP5 SWAP1 SWAP4 SWAP1 SWAP3 PUSH32 0x8C5BE1E5EBEC7D5BD14F71427D1E84F3DD0314C0F7B2291E5B200AC8C7C3B925 SWAP3 DUP3 SWAP1 SUB ADD SWAP1 LOG3 POP PUSH1 0x1 JUMPDEST SWAP3 SWAP2 POP POP JUMP JUMPDEST PUSH1 0x0 DUP1 DUP1 MSTORE PUSH1 0x6 PUSH1 0x20 MSTORE PUSH32 0x54CDD369E4E8A8515E52CA72EC816C2101831AD1F18BF44102ED171459C9B4F8 SLOAD PUSH1 0x5 SLOAD PUSH2 0x4DC SWAP2 PUSH4 0xFFFFFFFF PUSH2 0xA4B AND JUMP JUMPDEST SWAP1 POP SWAP1 JUMP JUMPDEST PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB DUP4 AND PUSH1 0x0 SWAP1 DUP2 MSTORE PUSH1 0x6 PUSH1 0x20 MSTORE PUSH1 0x40 DUP2 KECCAK256 SLOAD PUSH2 0x50A SWAP1 DUP4 PUSH4 0xFFFFFFFF PUSH2 0xA4B AND JUMP JUMPDEST PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB DUP6 AND PUSH1 0x0 SWAP1 DUP2 MSTORE PUSH1 0x6 PUSH1 0x20 SWAP1 DUP2 MSTORE PUSH1 0x40 DUP1 DUP4 KECCAK256 SWAP4 SWAP1 SWAP4 SSTORE PUSH1 0x7 DUP2 MSTORE DUP3 DUP3 KECCAK256 CALLER DUP4 MSTORE SWAP1 MSTORE KECCAK256 SLOAD PUSH2 0x547 SWAP1 DUP4 PUSH4 0xFFFFFFFF PUSH2 0xA4B AND JUMP JUMPDEST PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB DUP1 DUP7 AND PUSH1 0x0 SWAP1 DUP2 MSTORE PUSH1 0x7 PUSH1 0x20 SWAP1 DUP2 MSTORE PUSH1 0x40 DUP1 DUP4 KECCAK256 CALLER DUP5 MSTORE DUP3 MSTORE DUP1 DUP4 KECCAK256 SWAP5 SWAP1 SWAP5 SSTORE SWAP2 DUP7 AND DUP2 MSTORE PUSH1 0x6 SWAP1 SWAP2 MSTORE KECCAK256 SLOAD PUSH2 0x58B SWAP1 DUP4 PUSH4 0xFFFFFFFF PUSH2 0xA60 AND JUMP JUMPDEST PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB DUP1 DUP6 AND PUSH1 0x0 DUP2 DUP2 MSTORE PUSH1 0x6 PUSH1 0x20 SWAP1 DUP2 MSTORE PUSH1 0x40 SWAP2 DUP3 SWAP1 KECCAK256 SWAP5 SWAP1 SWAP5 SSTORE DUP1 MLOAD DUP7 DUP2 MSTORE SWAP1 MLOAD SWAP2 SWAP4 SWAP3 DUP9 AND SWAP3 PUSH32 0xDDF252AD1BE2C89B69C2B068FC378DAA952BA7F163C4A11628F55A4DF523B3EF SWAP3 SWAP2 DUP3 SWAP1 SUB ADD SWAP1 LOG3 POP PUSH1 0x1 SWAP4 SWAP3 POP POP POP JUMP JUMPDEST PUSH1 0x4 SLOAD PUSH1 0xFF AND DUP2 JUMP JUMPDEST PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB AND PUSH1 0x0 SWAP1 DUP2 MSTORE PUSH1 0x6 PUSH1 0x20 MSTORE PUSH1 0x40 SWAP1 KECCAK256 SLOAD SWAP1 JUMP JUMPDEST PUSH1 0x1 SLOAD PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB AND CALLER EQ PUSH2 0x627 JUMPI PUSH1 0x0 DUP1 REVERT JUMPDEST PUSH1 0x1 SLOAD PUSH1 0x0 DUP1 SLOAD PUSH1 0x40 MLOAD PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB SWAP4 DUP5 AND SWAP4 SWAP1 SWAP2 AND SWAP2 PUSH32 0x8BE0079C531659141344CD1FD0A4F28419497F9722A3DAAFE3B4186F6B6457E0 SWAP2 LOG3 PUSH1 0x1 DUP1 SLOAD PUSH1 0x0 DUP1 SLOAD PUSH20 0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF NOT SWAP1 DUP2 AND PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB DUP5 AND OR SWAP1 SWAP2 SSTORE AND SWAP1 SSTORE JUMP JUMPDEST PUSH1 0x0 SLOAD PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB AND DUP2 JUMP JUMPDEST PUSH1 0x2 DUP1 SLOAD PUSH1 0x40 DUP1 MLOAD PUSH1 0x20 PUSH1 0x1 DUP5 AND ISZERO PUSH2 0x100 MUL PUSH1 0x0 NOT ADD SWAP1 SWAP4 AND DUP5 SWAP1 DIV PUSH1 0x1F DUP2 ADD DUP5 SWAP1 DIV DUP5 MUL DUP3 ADD DUP5 ADD SWAP1 SWAP3 MSTORE DUP2 DUP2 MSTORE SWAP3 SWAP2 DUP4 ADD DUP3 DUP3 DUP1 ISZERO PUSH2 0x42F JUMPI DUP1 PUSH1 0x1F LT PUSH2 0x404 JUMPI PUSH2 0x100 DUP1 DUP4 SLOAD DIV MUL DUP4 MSTORE SWAP2 PUSH1 0x20 ADD SWAP2 PUSH2 0x42F JUMP JUMPDEST CALLER PUSH1 0x0 SWAP1 DUP2 MSTORE PUSH1 0x6 PUSH1 0x20 MSTORE PUSH1 0x40 DUP2 KECCAK256 SLOAD PUSH2 0x71F SWAP1 DUP4 PUSH4 0xFFFFFFFF PUSH2 0xA4B AND JUMP JUMPDEST CALLER PUSH1 0x0 SWAP1 DUP2 MSTORE PUSH1 0x6 PUSH1 0x20 MSTORE PUSH1 0x40 DUP1 DUP3 KECCAK256 SWAP3 SWAP1 SWAP3 SSTORE PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB DUP6 AND DUP2 MSTORE KECCAK256 SLOAD PUSH2 0x751 SWAP1 DUP4 PUSH4 0xFFFFFFFF PUSH2 0xA60 AND JUMP JUMPDEST PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB DUP5 AND PUSH1 0x0 DUP2 DUP2 MSTORE PUSH1 0x6 PUSH1 0x20 SWAP1 DUP2 MSTORE PUSH1 0x40 SWAP2 DUP3 SWAP1 KECCAK256 SWAP4 SWAP1 SWAP4 SSTORE DUP1 MLOAD DUP6 DUP2 MSTORE SWAP1 MLOAD SWAP2 SWAP3 CALLER SWAP3 PUSH32 0xDDF252AD1BE2C89B69C2B068FC378DAA952BA7F163C4A11628F55A4DF523B3EF SWAP3 DUP2 SWAP1 SUB SWAP1 SWAP2 ADD SWAP1 LOG3 POP PUSH1 0x1 SWAP3 SWAP2 POP POP JUMP JUMPDEST CALLER PUSH1 0x0 DUP2 DUP2 MSTORE PUSH1 0x7 PUSH1 0x20 SWAP1 DUP2 MSTORE PUSH1 0x40 DUP1 DUP4 KECCAK256 PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB DUP9 AND DUP1 DUP6 MSTORE SWAP1 DUP4 MSTORE DUP2 DUP5 KECCAK256 DUP8 SWAP1 SSTORE DUP2 MLOAD DUP8 DUP2 MSTORE SWAP2 MLOAD SWAP4 SWAP5 SWAP1 SWAP4 SWAP1 SWAP3 PUSH32 0x8C5BE1E5EBEC7D5BD14F71427D1E84F3DD0314C0F7B2291E5B200AC8C7C3B925 SWAP3 DUP3 SWAP1 SUB ADD SWAP1 LOG3 PUSH1 0x40 MLOAD PUSH32 0x8F4FFCB100000000000000000000000000000000000000000000000000000000 DUP2 MSTORE CALLER PUSH1 0x4 DUP3 ADD DUP2 DUP2 MSTORE PUSH1 0x24 DUP4 ADD DUP7 SWAP1 MSTORE ADDRESS PUSH1 0x44 DUP5 ADD DUP2 SWAP1 MSTORE PUSH1 0x80 PUSH1 0x64 DUP6 ADD SWAP1 DUP2 MSTORE DUP7 MLOAD PUSH1 0x84 DUP7 ADD MSTORE DUP7 MLOAD PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB DUP11 AND SWAP6 PUSH4 0x8F4FFCB1 SWAP6 SWAP5 DUP11 SWAP5 SWAP4 DUP11 SWAP4 SWAP2 SWAP3 SWAP1 SWAP2 PUSH1 0xA4 SWAP1 SWAP2 ADD SWAP1 PUSH1 0x20 DUP6 ADD SWAP1 DUP1 DUP4 DUP4 PUSH1 0x0 JUMPDEST DUP4 DUP2 LT ISZERO PUSH2 0x89F JUMPI DUP2 DUP2 ADD MLOAD DUP4 DUP3 ADD MSTORE PUSH1 0x20 ADD PUSH2 0x887 JUMP JUMPDEST POP POP POP POP SWAP1 POP SWAP1 DUP2 ADD SWAP1 PUSH1 0x1F AND DUP1 ISZERO PUSH2 0x8CC JUMPI DUP1 DUP3 SUB DUP1 MLOAD PUSH1 0x1 DUP4 PUSH1 0x20 SUB PUSH2 0x100 EXP SUB NOT AND DUP2 MSTORE PUSH1 0x20 ADD SWAP2 POP JUMPDEST POP SWAP6 POP POP POP POP POP POP PUSH1 0x0 PUSH1 0x40 MLOAD DUP1 DUP4 SUB DUP2 PUSH1 0x0 DUP8 DUP1 EXTCODESIZE ISZERO DUP1 ISZERO PUSH2 0x8EE JUMPI PUSH1 0x0 DUP1 REVERT JUMPDEST POP GAS CALL ISZERO DUP1 ISZERO PUSH2 0x902 JUMPI RETURNDATASIZE PUSH1 0x0 DUP1 RETURNDATACOPY RETURNDATASIZE PUSH1 0x0 REVERT JUMPDEST POP PUSH1 0x1 SWAP8 SWAP7 POP POP POP POP POP POP POP JUMP JUMPDEST PUSH1 0x1 SLOAD PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB AND DUP2 JUMP JUMPDEST PUSH1 0x0 DUP1 SLOAD PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB AND CALLER EQ PUSH2 0x937 JUMPI PUSH1 0x0 DUP1 REVERT JUMPDEST PUSH1 0x0 DUP1 SLOAD PUSH1 0x40 DUP1 MLOAD PUSH32 0xA9059CBB00000000000000000000000000000000000000000000000000000000 DUP2 MSTORE PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB SWAP3 DUP4 AND PUSH1 0x4 DUP3 ADD MSTORE PUSH1 0x24 DUP2 ADD DUP7 SWAP1 MSTORE SWAP1 MLOAD SWAP2 DUP7 AND SWAP3 PUSH4 0xA9059CBB SWAP3 PUSH1 0x44 DUP1 DUP5 ADD SWAP4 PUSH1 0x20 SWAP4 SWAP1 DUP4 SWAP1 SUB SWAP1 SWAP2 ADD SWAP1 DUP3 SWAP1 DUP8 DUP1 EXTCODESIZE ISZERO DUP1 ISZERO PUSH2 0x9A7 JUMPI PUSH1 0x0 DUP1 REVERT JUMPDEST POP GAS CALL ISZERO DUP1 ISZERO PUSH2 0x9BB JUMPI RETURNDATASIZE PUSH1 0x0 DUP1 RETURNDATACOPY RETURNDATASIZE PUSH1 0x0 REVERT JUMPDEST POP POP POP POP PUSH1 0x40 MLOAD RETURNDATASIZE PUSH1 0x20 DUP2 LT ISZERO PUSH2 0x9D1 JUMPI PUSH1 0x0 DUP1 REVERT JUMPDEST POP MLOAD SWAP4 SWAP3 POP POP POP JUMP JUMPDEST PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB SWAP2 DUP3 AND PUSH1 0x0 SWAP1 DUP2 MSTORE PUSH1 0x7 PUSH1 0x20 SWAP1 DUP2 MSTORE PUSH1 0x40 DUP1 DUP4 KECCAK256 SWAP4 SWAP1 SWAP5 AND DUP3 MSTORE SWAP2 SWAP1 SWAP2 MSTORE KECCAK256 SLOAD SWAP1 JUMP JUMPDEST PUSH1 0x0 SLOAD PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB AND CALLER EQ PUSH2 0xA1C JUMPI PUSH1 0x0 DUP1 REVERT JUMPDEST PUSH1 0x1 DUP1 SLOAD PUSH20 0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF NOT AND PUSH1 0x1 PUSH1 0xA0 PUSH1 0x2 EXP SUB SWAP3 SWAP1 SWAP3 AND SWAP2 SWAP1 SWAP2 OR SWAP1 SSTORE JUMP JUMPDEST PUSH1 0x0 DUP3 DUP3 GT ISZERO PUSH2 0xA5A JUMPI PUSH1 0x0 DUP1 REVERT JUMPDEST POP SWAP1 SUB SWAP1 JUMP JUMPDEST DUP2 DUP2 ADD DUP3 DUP2 LT ISZERO PUSH2 0x498 JUMPI PUSH1 0x0 DUP1 REVERT STOP LOG1 PUSH6 0x627A7A723058 KECCAK256 BYTE 0x25 0xe9 0xb3 0xd9 ISZERO 0xe9 0x4e MSIZE 0xe4 CALLDATASIZE 0x4c MSTORE CALLER SWAP7 PUSH31 0x6BB103E29668744F14E9086F65526CFC002900000000000000000000000000 ",
	"sourceMap": "3558:5426:0:-;;;4036:268;8:9:-1;5:2;;;30:1;27;20:12;5:2;-1:-1;2876:5:0;:18;;-1:-1:-1;;;;;;2876:18:0;2884:10;2876:18;;;4068:14;;;;;;;;;;;;;;;;;;;;;;;:6;;:14;:::i;:::-;-1:-1:-1;4093:25:0;;;;;;;;;;;;;;;;;;;;;;;:4;;:25;:::i;:::-;-1:-1:-1;4129:8:0;:13;;4140:2;-1:-1:-1;;4129:13:0;;;;;;;;;4189:8;4180:2;:18;4168:9;:30;4153:12;:45;;;-1:-1:-1;4218:5:0;;-1:-1:-1;;;;;4218:5:0;;;4209:15;;:8;:15;;;;;;;;:30;;;4276:5;;4255:41;;;;;;;4276:5;;;4255:41;;;;;;;;;;;3558:5426;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;-1:-1:-1;3558:5426:0;;;-1:-1:-1;3558:5426:0;:::i;:::-;;;:::o;:::-;;;;;;;;;;;;;;;;;;;;:::o;:::-;;;;;;;"
}


[
	{
		"constant": true,
		"inputs": [],
		"name": "name",
		"outputs": [
			{
				"name": "",
				"type": "string"
			}
		],
		"payable": false,
		"stateMutability": "view",
		"type": "function"
	},
	{
		"constant": false,
		"inputs": [
			{
				"name": "spender",
				"type": "address"
			},
			{
				"name": "tokens",
				"type": "uint256"
			}
		],
		"name": "approve",
		"outputs": [
			{
				"name": "success",
				"type": "bool"
			}
		],
		"payable": false,
		"stateMutability": "nonpayable",
		"type": "function"
	},
	{
		"constant": true,
		"inputs": [],
		"name": "totalSupply",
		"outputs": [
			{
				"name": "",
				"type": "uint256"
			}
		],
		"payable": false,
		"stateMutability": "view",
		"type": "function"
	},
	{
		"constant": false,
		"inputs": [
			{
				"name": "from",
				"type": "address"
			},
			{
				"name": "to",
				"type": "address"
			},
			{
				"name": "tokens",
				"type": "uint256"
			}
		],
		"name": "transferFrom",
		"outputs": [
			{
				"name": "success",
				"type": "bool"
			}
		],
		"payable": false,
		"stateMutability": "nonpayable",
		"type": "function"
	},
	{
		"constant": true,
		"inputs": [],
		"name": "decimals",
		"outputs": [
			{
				"name": "",
				"type": "uint8"
			}
		],
		"payable": false,
		"stateMutability": "view",
		"type": "function"
	},
	{
		"constant": true,
		"inputs": [
			{
				"name": "tokenOwner",
				"type": "address"
			}
		],
		"name": "balanceOf",
		"outputs": [
			{
				"name": "balance",
				"type": "uint256"
			}
		],
		"payable": false,
		"stateMutability": "view",
		"type": "function"
	},
	{
		"constant": false,
		"inputs": [],
		"name": "acceptOwnership",
		"outputs": [],
		"payable": false,
		"stateMutability": "nonpayable",
		"type": "function"
	},
	{
		"constant": true,
		"inputs": [],
		"name": "owner",
		"outputs": [
			{
				"name": "",
				"type": "address"
			}
		],
		"payable": false,
		"stateMutability": "view",
		"type": "function"
	},
	{
		"constant": true,
		"inputs": [],
		"name": "symbol",
		"outputs": [
			{
				"name": "",
				"type": "string"
			}
		],
		"payable": false,
		"stateMutability": "view",
		"type": "function"
	},
	{
		"constant": false,
		"inputs": [
			{
				"name": "to",
				"type": "address"
			},
			{
				"name": "tokens",
				"type": "uint256"
			}
		],
		"name": "transfer",
		"outputs": [
			{
				"name": "success",
				"type": "bool"
			}
		],
		"payable": false,
		"stateMutability": "nonpayable",
		"type": "function"
	},
	{
		"constant": false,
		"inputs": [
			{
				"name": "spender",
				"type": "address"
			},
			{
				"name": "tokens",
				"type": "uint256"
			},
			{
				"name": "data",
				"type": "bytes"
			}
		],
		"name": "approveAndCall",
		"outputs": [
			{
				"name": "success",
				"type": "bool"
			}
		],
		"payable": false,
		"stateMutability": "nonpayable",
		"type": "function"
	},
	{
		"constant": true,
		"inputs": [],
		"name": "newOwner",
		"outputs": [
			{
				"name": "",
				"type": "address"
			}
		],
		"payable": false,
		"stateMutability": "view",
		"type": "function"
	},
	{
		"constant": false,
		"inputs": [
			{
				"name": "tokenAddress",
				"type": "address"
			},
			{
				"name": "tokens",
				"type": "uint256"
			}
		],
		"name": "transferAnyERC20Token",
		"outputs": [
			{
				"name": "success",
				"type": "bool"
			}
		],
		"payable": false,
		"stateMutability": "nonpayable",
		"type": "function"
	},
	{
		"constant": true,
		"inputs": [
			{
				"name": "tokenOwner",
				"type": "address"
			},
			{
				"name": "spender",
				"type": "address"
			}
		],
		"name": "allowance",
		"outputs": [
			{
				"name": "remaining",
				"type": "uint256"
			}
		],
		"payable": false,
		"stateMutability": "view",
		"type": "function"
	},
	{
		"constant": false,
		"inputs": [
			{
				"name": "_newOwner",
				"type": "address"
			}
		],
		"name": "transferOwnership",
		"outputs": [],
		"payable": false,
		"stateMutability": "nonpayable",
		"type": "function"
	},
	{
		"inputs": [],
		"payable": false,
		"stateMutability": "nonpayable",
		"type": "constructor"
	},
	{
		"payable": true,
		"stateMutability": "payable",
		"type": "fallback"
	},
	{
		"anonymous": false,
		"inputs": [
			{
				"indexed": true,
				"name": "_from",
				"type": "address"
			},
			{
				"indexed": true,
				"name": "_to",
				"type": "address"
			}
		],
		"name": "OwnershipTransferred",
		"type": "event"
	},
	{
		"anonymous": false,
		"inputs": [
			{
				"indexed": true,
				"name": "from",
				"type": "address"
			},
			{
				"indexed": true,
				"name": "to",
				"type": "address"
			},
			{
				"indexed": false,
				"name": "tokens",
				"type": "uint256"
			}
		],
		"name": "Transfer",
		"type": "event"
	},
	{
		"anonymous": false,
		"inputs": [
			{
				"indexed": true,
				"name": "tokenOwner",
				"type": "address"
			},
			{
				"indexed": true,
				"name": "spender",
				"type": "address"
			},
			{
				"indexed": false,
				"name": "tokens",
				"type": "uint256"
			}
		],
		"name": "Approval",
		"type": "event"
	}
]


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
