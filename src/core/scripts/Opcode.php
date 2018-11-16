<?php

namespace ontio\core\scripts;

class Opcode
{
    // Constants
  const PUSH0 = 0x00; // An empty array of bytes is pushed onto the stack.
  const PUSHF = PUSH0;
  const PUSHBYTES1 = 0x01; // 0x01-0x4B The next bytes is data to be pushed onto the stack
  const PUSHBYTES75 = 0x4B;
  const PUSHDATA1 = 0x4C; // The next byte contains the number of bytes to be pushed onto the stack.
  const PUSHDATA2 = 0x4D; // The next two bytes contain the number of bytes to be pushed onto the stack.
  const PUSHDATA4 = 0x4E; // The next four bytes contain the number of bytes to be pushed onto the stack.
  const PUSHM1 = 0x4F; // The number -1 is pushed onto the stack.
  const PUSH1 = 0x51; // The number 1 is pushed onto the stack.
  const PUSHT = PUSH1;
  const PUSH2 = 0x52; // The number 2 is pushed onto the stack.
  const PUSH3 = 0x53; // The number 3 is pushed onto the stack.
  const PUSH4 = 0x54; // The number 4 is pushed onto the stack.
  const PUSH5 = 0x55; // The number 5 is pushed onto the stack.
  const PUSH6 = 0x56; // The number 6 is pushed onto the stack.
  const PUSH7 = 0x57; // The number 7 is pushed onto the stack.
  const PUSH8 = 0x58; // The number 8 is pushed onto the stack.
  const PUSH9 = 0x59; // The number 9 is pushed onto the stack.
  const PUSH10 = 0x5A; // The number 10 is pushed onto the stack.
  const PUSH11 = 0x5B; // The number 11 is pushed onto the stack.
  const PUSH12 = 0x5C; // The number 12 is pushed onto the stack.
  const PUSH13 = 0x5D; // The number 13 is pushed onto the stack.
  const PUSH14 = 0x5E; // The number 14 is pushed onto the stack.
  const PUSH15 = 0x5F; // The number 15 is pushed onto the stack.
  const PUSH16 = 0x60; // The number 16 is pushed onto the stack.

  // Flow control
  const NOP = 0x61; // Does nothing.
  const JMP = 0x62;
  const JMPIF = 0x63;
  const JMPIFNOT = 0x64;
  const CALL = 0x65;
  const RET = 0x66;
  const APPCALL = 0x67;
  const SYSCALL = 0x68;
  const TAILCALL = 0x69;
  const DUPFROMALTSTACK = 0x6A;

  // Stack
  const TOALTSTACK = 0x6B; // Puts the input onto the top of the alt stack. Removes it from the main stack.
  const FROMALTSTACK = 0x6C; // Puts the input onto the top of the main stack. Removes it from the alt stack.
  const XDROP = 0x6D;
  const XSWAP = 0x72;
  const XTUCK = 0x73;
  const DEPTH = 0x74; // Puts the number of stack items onto the stack.
  const DROP = 0x75; // Removes the top stack item.
  const DUP = 0x76; // Duplicates the top stack item.
  const NIP = 0x77; // Removes the second-to-top stack item.
  const OVER = 0x78; // Copies the second-to-top stack item to the top.
  const PICK = 0x79; // The item n back in the stack is copied to the top.
  const ROLL = 0x7A; // The item n back in the stack is moved to the top.
  const ROT = 0x7B; // The top three items on the stack are rotated to the left.
  const SWAP = 0x7C; // The top two items on the stack are swapped.
  const TUCK = 0x7D; // The item at the top of the stack is copied and inserted before the second-to-top item.

  // Splice
  const CAT = 0x7E; // Concatenates two strings.
  const SUBSTR = 0x7F; // Returns a section of a string.
  const LEFT = 0x80; // Keeps only characters left of the specified point in a string.
  const RIGHT = 0x81; // Keeps only characters right of the specified point in a string.
  const SIZE = 0x82; // Returns the length of the input string.

  // Bitwise logic
  const INVERT = 0x83; // Flips all of the bits in the input.
  const and = 0x84; // Boolean and between each bit in the inputs.
  const or = 0x85; // Boolean or between each bit in the inputs.
  const xor = 0x86; // Boolean exclusive or between each bit in the inputs.
  const EQUAL = 0x87; // Returns 1 if the inputs are exactly equal; 0 otherwise.
  // EQUALVERIFY = 0x88; // Same as EQUAL; but runs VERIFY afterward.
  // RESERVED1 = 0x89; // Transaction is invalid unless occuring in an unexecuted IF branch
  // RESERVED2 = 0x8A; // Transaction is invalid unless occuring in an unexecuted IF branch

  // Arithmetic
  // Note: Arithmetic inputs are limited to signed 32-bit integers; but may overflow their output.
  const INC = 0x8B; // 1 is added to the input.
  const DEC = 0x8C; // 1 is subtracted from the input.
  // SAL           = 0x8D; // The input is multiplied by 2.
  // SAR           = 0x8E; // The input is divided by 2.
  const NEGATE = 0x8F; // The sign of the input is flipped.
  const ABS = 0x90; // The input is made positive.
  const NOT = 0x91; // If the input is 0 or 1; it is flipped. Otherwise the output will be 0.
  const NZ = 0x92; // Returns 0 if the input is 0. 1 otherwise.
  const ADD = 0x93; // a is added to b.
  const SUB = 0x94; // b is subtracted from a.
  const MUL = 0x95; // a is multiplied by b.
  const DIV = 0x96; // a is divided by b.
  const MOD = 0x97; // Returns the remainder after dividing a by b.
  const SHL = 0x98; // Shifts a left b bits; preserving sign.
  const SHR = 0x99; // Shifts a right b bits; preserving sign.
  const BOOLAND = 0x9A; // If both a and b are not 0; the output is 1. Otherwise 0.
  const BOOLOR = 0x9B; // If a or b is not 0; the output is 1. Otherwise 0.
  const NUMEQUAL = 0x9C; // Returns 1 if the numbers are equal; 0 otherwise.
  const NUMNOTEQUAL = 0x9E; // Returns 1 if the numbers are not equal; 0 otherwise.
  const LT = 0x9F; // Returns 1 if a is less than b; 0 otherwise.
  const GT = 0xA0; // Returns 1 if a is greater than b; 0 otherwise.
  const LTE = 0xA1; // Returns 1 if a is less than or equal to b; 0 otherwise.
  const GTE = 0xA2; // Returns 1 if a is greater than or equal to b; 0 otherwise.
  const MIN = 0xA3; // Returns the smaller of a and b.
  const MAX = 0xA4; // Returns the larger of a and b.
  const WITHIN = 0xA5; // Returns 1 if x is within the specified range (left-inclusive); 0 otherwise.

  // Crypto
  // RIPEMD160 = 0xA6; // The input is hashed using RIPEMD-160.
  const SHA1 = 0xA7; // The input is hashed using SHA-1.
  const SHA256 = 0xA8; // The input is hashed using SHA-256.
  const HASH160 = 0xA9;
  const HASH256 = 0xAA;
  const CHECKSIG = 0xAC; // The entire transaction's outputs inputs and script (from the most recently-executed CODESEPARATOR to the end) are hashed. The signature used by CHECKSIG must be a valid signature for this hash and public key. If it is 1 is returned 0 otherwise.
  const CHECKMULTISIG = 0xAE; // For each signature and public key pair CHECKSIG is executed. If more public keys than signatures are listed some key/sig pairs can fail. All signatures need to match a public key. If all signatures are valid 1 is returned 0 otherwise. Due to a bug one extra unused value is removed from the stack.

  // Array
  const ARRAYSIZE = 0xC0;
  const PACK = 0xC1;
  const UNPACK = 0xC2;
  const PICKITEM = 0xC3;
  const SETITEM = 0xC4;
  const NEWARRAY = 0xC5;
  const NEWSTRUCT = 0xC6;
  const NEWMAP = 0xC7;
  const APPEND = 0xC8;
  const REVERSE = 0xC9;
  const REMOVE = 0xCA;
  const HASKEY = 0xCB;
  const KEYS = 0xCC;
  const VALUES = 0xCD;

	// Exceptionthrow = 0xF0 ;
  const THROWIFNOT = 0xF1;
}
